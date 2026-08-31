<?php

namespace App\Traits;

use App\Models\Batch;
use App\Models\FeedingLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait ManagesFeedingSchedules
{
    public function getUpcomingFeedingsForBatches(Collection $batches): Collection
    {
        Log::info('Starting getUpcomingFeedingsForBatches');
        $now = Carbon::now();
        Log::info('Current time ($now): ' . $now->toDateTimeString());
        Log::info('Number of batches received: ' . $batches->count());

        $schedules = collect();

        foreach ($batches as $batch) {
            Log::info('Processing batch: ' . $batch->name . ' (ID: ' . $batch->id . ') with method: ' . $batch->feeding_method);

            if ($batch->feeding_method === 'unlimited') {
                $schedules->push((object)[
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->name,
                    'schedule_time' => 'Any time',
                    'type' => 'Unlimited',
                    'status' => 'due', // Always available to be fed
                ]);
                Log::info('Added unlimited schedule for batch: ' . $batch->name);
                continue; // Move to the next batch
            }

            // Handle scheduled feedings
            $feedTimes = $this->getFeedTimesForBatch($batch);
            Log::info('Calculated feed times for batch ' . $batch->name . ': ' . json_encode($feedTimes));

            foreach ($feedTimes as $feedConfig) {
                $feedTime = $feedConfig['time'];
                $status = '';

                $alreadyFed = $this->wasBatchFedForSchedule($batch, $feedTime, $batch->feeding_method);
                Log::info('Is batch ' . $batch->name . ' already fed for ' . $feedTime->format('g:i A') . '? ' . ($alreadyFed ? 'Yes' : 'No'));

                if ($alreadyFed) {
                    $status = 'fed';
                } else {
                    $status = $this->getScheduleStatus($now, $feedTime);
                }
                Log::info('Status for ' . $feedTime->format('g:i A') . ' is: ' . $status);

                if ($status) {
                    $schedules->push((object)[
                        'batch_id' => $batch->id,
                        'batch_name' => $batch->name,
                        'schedule_time' => $feedTime->format('g:i A'),
                        'type' => $feedConfig['type'],
                        'status' => $status,
                    ]);
                }
            }
        }

        Log::info('Total schedules calculated: ' . $schedules->count());
        return $schedules->sortBy(function ($schedule) {
            switch ($schedule->status) {
                case 'due': return 0;
                case 'missed': return 1;
                case 'upcoming': return 2;
                case 'fed': return 3;
                default: return 4;
            }
        });
    }

    private function getFeedTimesForBatch(Batch $batch): array
    {
        $times = [];
        $today = Carbon::today();

        switch ($batch->feeding_method) {
            case 'twice_daily':
                $times = [
                    ['time' => $today->copy()->setHour(7), 'type' => 'Twice a Day'],
                    ['time' => $today->copy()->setHour(19), 'type' => 'Twice a Day'],
                ];
                break;
            case 'four_times_daily':
                for ($i = 0; $i < 4; $i++) {
                    $times[] = [
                        'time' => $today->copy()->setHour(7)->addHours($i * 4),
                        'type' => 'Four Times a Day'
                    ];
                }
                break;
        }
        return $times;
    }

    private function getScheduleStatus(Carbon $now, Carbon $feedTime): string
    {
        // "Due" window starts 30 mins before schedule and ends 1 hour after.
        $startTime = $feedTime->copy()->subMinutes(30);
        $endTime = $feedTime->copy()->addHour();

        if ($now->between($startTime, $endTime)) {
            return 'due';
        }

        if ($now->gt($endTime)) {
            return 'missed';
        }

        return 'upcoming';
    }

    private function wasBatchFedForSchedule(Batch $batch, Carbon $feedTime, string $feedingMethod): bool
    {
        $today = Carbon::today();

        // Define the check window based on the feeding method
        list($startTime, $endTime) = [null, null];

        if ($feedingMethod === 'twice_daily') {
            // For twice daily, check a window around the 7am and 7pm slots
            if ($feedTime->hour == 7) {
                $startTime = $today->copy()->setHour(6); // 6 AM
                $endTime = $today->copy()->setHour(11); // 11 AM
            } else { // 7 PM
                $startTime = $today->copy()->setHour(18); // 6 PM
                $endTime = $today->copy()->setHour(23); // 11 PM
            }
        } elseif ($feedingMethod === 'four_times_daily') {
            // For four times daily, the window is between the current and next feed time
            $startTime = $feedTime->copy()->subHour(); // 1 hour before
            $endTime = $feedTime->copy()->addHours(3); // 3 hours after, before next 4-hour slot
        }

        // If we have a valid window, check for a log
        if ($startTime && $endTime) {
            return FeedingLog::where('batch_id', $batch->id)
                ->whereBetween('feeding_time', [$startTime, $endTime])
                ->exists();
        }

        return false;
    }
}