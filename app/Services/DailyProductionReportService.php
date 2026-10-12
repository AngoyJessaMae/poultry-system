<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\ProductionStandard;
use Carbon\Carbon;

class DailyProductionReportService
{
    public function rows(Batch $batch, ?Carbon $until = null): array
    {
        $start = $batch->arrival_date->copy()->startOfDay();
        $end = ($until ?? now())->copy()->startOfDay();

        $dailyRecords = $batch->dailyProductionRecords()
            ->whereBetween('recorded_date', [$start, $end])
            ->get()
            ->keyBy(fn ($record) => $record->recorded_date->toDateString());

        $mortality = $batch->mortalityRecords()
            ->whereBetween('mortality_date', [$start, $end])
            ->get()
            ->groupBy(fn ($record) => $record->mortality_date->toDateString())
            ->map(fn ($records) => $records->sum('count'));

        $growth = $batch->growthRecords()
            ->whereBetween('recorded_date', [$start, $end])
            ->orderBy('recorded_date')
            ->get()
            ->groupBy(fn ($record) => $record->recorded_date->toDateString())
            ->map(fn ($records) => $records->last()->average_weight_grams);

        $feeding = $batch->feedingLogs()
            ->whereBetween('feeding_time', [$start, $end->copy()->endOfDay()])
            ->get()
            ->groupBy(fn ($record) => $record->feeding_time->toDateString())
            ->map(fn ($records) => [
                'grams' => $records->sum('quantity'),
                'feed_type' => $records->pluck('feed_type')->filter()->unique()->implode(', '),
            ]);

        $standards = ProductionStandard::query()->orderBy('age_days')->get();
        $cumulativeDeaths = 0;
        $cumulativeFeed = 0;
        $cumulativeStandardFeed = 0;
        $rows = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateKey = $date->toDateString();
            $age = (int) $start->diffInDays($date) + 1;
            $record = $dailyRecords->get($dateKey);
            $legacyFeed = $feeding->get($dateKey, ['grams' => null, 'feed_type' => null]);
            $standard = $standards->where('age_days', '<=', $age)->last();

            $dailyDeaths = $record?->mortality_heads ?? $mortality->get($dateKey, 0);
            $actualWeight = $record ? $record->actual_live_weight_grams : $growth->get($dateKey);
            $dailyFeed = $record?->actual_feed_grams ?? $legacyFeed['grams'];
            $feedType = $record?->feed_type ?: $legacyFeed['feed_type'];

            $cumulativeDeaths += (int) $dailyDeaths;
            if ($dailyFeed !== null) {
                $cumulativeFeed += (float) $dailyFeed;
            }
            if ($standard?->feed_grams !== null) {
                $cumulativeStandardFeed += (float) $standard->feed_grams;
            }

            $liveHeads = max(0, $batch->initial_quantity - $cumulativeDeaths);
            $rows[] = [
                'date' => $date->copy(),
                'age' => $age,
                'record' => $record,
                'mortality_heads' => (int) $dailyDeaths,
                'cumulative_deaths' => $cumulativeDeaths,
                'actual_mortality_percent' => $batch->initial_quantity > 0
                    ? ($cumulativeDeaths / $batch->initial_quantity) * 100
                    : null,
                'standard_mortality_percent' => $standard?->mortality_percent,
                'actual_live_weight_grams' => $actualWeight,
                'standard_live_weight_grams' => $standard?->live_weight_grams,
                'feed_type' => $feedType,
                'actual_feed_grams' => $dailyFeed,
                'cumulative_actual_feed_grams' => $cumulativeFeed,
                'standard_feed_grams' => $standard?->feed_grams,
                'cumulative_standard_feed_grams' => $cumulativeStandardFeed,
                'actual_fcr' => $actualWeight !== null && $liveHeads > 0 && $cumulativeFeed > 0
                    ? $cumulativeFeed / ($liveHeads * (float) $actualWeight)
                    : null,
                'standard_fcr' => $standard?->fcr,
                'live_heads' => $liveHeads,
            ];
        }

        return $rows;
    }
}