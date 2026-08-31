<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\BatchMovement;
use App\Models\FeedingSchedule;
use Illuminate\Support\Facades\Auth;

class BatchObserver
{
    public function created(Batch $batch)
    {
        $this->updateFeedingSchedules($batch);
    }

    public function updated(Batch $batch)
    {
        // Handle station change logging
        if ($batch->isDirty('station_id')) {
            BatchMovement::create([
                'batch_id' => $batch->id,
                'from_station_id' => $batch->getOriginal('station_id'),
                'to_station_id' => $batch->station_id,
                'user_id' => Auth::id(),
                'moved_at' => now(),
            ]);
        }

        // Handle automatic feeding schedule updates
        if ($batch->isDirty('feeding_method')) {
            $this->updateFeedingSchedules($batch);
        }
    }

    private function updateFeedingSchedules(Batch $batch)
    {
        // First, delete all existing schedules for this batch
        $batch->feedingSchedules()->delete();

        // Define the schedules based on the method
        $schedules = [
            'twice_daily' => ['08:00:00', '17:00:00'],
            'four_times_daily' => ['08:00:00', '12:00:00', '16:00:00', '20:00:00'],
        ];

        // Create new schedules if the method is in our list
        if (array_key_exists($batch->feeding_method, $schedules)) {
            foreach ($schedules[$batch->feeding_method] as $time) {
                FeedingSchedule::create([
                    'batch_id' => $batch->id,
                    'scheduled_time' => $time,
                ]);
            }
        }
    }
}