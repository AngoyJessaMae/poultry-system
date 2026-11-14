<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StationFeedingService
{
    public function configFor(Station $station): array
    {
        $config = $station->feedingConfig;
        if ($config) {
            return [
                'method' => $config->method,
                'times' => array_values($config->schedule_times ?? []),
            ];
        }

        return match (strtoupper((string) $station->station_code)) {
            'S1' => ['method' => 'unlimited', 'times' => []],
            'S2' => ['method' => 'four_times_daily', 'times' => ['07:00', '11:00', '15:00', '19:00']],
            default => ['method' => 'twice_daily', 'times' => []],
        };
    }

    public function schedulesFor(Batch $batch, Carbon $date): Collection
    {
        $station = $batch->station;
        $config = $this->configFor($station);

        if ($config['method'] === 'unlimited') {
            return collect([(object) [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'station_id' => $station->id,
                'date' => $date->toDateString(),
                'scheduled_at' => null,
                'schedule_time' => 'Any time',
                'status' => 'available',
            ]]);
        }

        return collect($config['times'])->map(function (string $time) use ($batch, $station, $date) {
            $scheduledAt = Carbon::parse($date->toDateString() . ' ' . $time);
            $completed = FeedingLog::where('batch_id', $batch->id)
                ->where('station_id', $station->id)
                ->where('scheduled_at', $scheduledAt)
                ->exists();

            return (object) [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'station_id' => $station->id,
                'date' => $date->toDateString(),
                'scheduled_at' => $scheduledAt,
                'schedule_time' => $scheduledAt->format('g:i A'),
                'status' => $completed ? 'completed' : 'pending',
            ];
        });
    }
}
