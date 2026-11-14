<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\HealthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonitoringAnalyticsService
{
    public function managerOverview(int $year, ?int $month = null): array
    {
        $periodStart = Carbon::create($year, $month ?: 1, 1)->startOfMonth();
        $periodEnd = $month
            ? $periodStart->copy()->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfDay();

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'label' => $month ? $periodStart->format('F Y') : (string) $year,
            ],
            'stations' => $this->stationOverview(),
            'batches' => Batch::with('station')->active()->orderBy('batch_code')->get(),
            'feeding' => $this->feedingTrend($periodStart, $periodEnd),
            'growth' => $this->growthTrend($periodStart, $periodEnd),
            'health' => $this->healthTrend($periodStart, $periodEnd),
            'production' => $this->productionTrend($periodStart, $periodEnd),
            'sales' => $this->salesTrend($periodStart, $periodEnd),
        ];
    }

    private function stationOverview(): Collection
    {
        return Station::query()
            ->whereIn('station_code', ['S1', 'S2', 'S3'])
            ->with(['batches' => fn ($query) => $query->active()->orderBy('batch_code')])
            ->orderBy('station_code')
            ->get()
            ->map(fn (Station $station) => [
                'station' => $station,
                'batch_count' => $station->batches->count(),
                'quantity' => (int) $station->batches->sum('current_quantity'),
                'batches' => $station->batches,
            ]);
    }

    private function feedingTrend(Carbon $start, Carbon $end): array
    {
        $records = FeedingLog::query()
            ->with('station')
            ->whereBetween('feeding_time', [$start, $end])
            ->get();

        $daily = $this->dateSeries($start, $end)->map(function (Carbon $date) use ($records) {
            $matching = $records->filter(fn ($record) => $record->feeding_time->isSameDay($date));

            return [
                'label' => $date->format('M j'),
                'kg' => round((float) $matching->sum(fn ($record) => $record->quantity_in_kg), 3),
            ];
        });

        $stations = $records->groupBy(fn ($record) => $record->station?->station_code ?: 'Unknown')
            ->map(fn ($items, $label) => ['label' => $label, 'kg' => round((float) $items->sum(fn ($record) => $record->quantity_in_kg), 3)])
            ->values();

        return ['daily' => $daily, 'stations' => $stations, 'total_kg' => round((float) $records->sum(fn ($record) => $record->quantity_in_kg), 3)];
    }

    private function growthTrend(Carbon $start, Carbon $end): Collection
    {
        return GrowthRecord::query()
            ->whereBetween('recorded_date', [$start, $end])
            ->get()
            ->groupBy(fn ($record) => $record->recorded_date->format('Y-m'))
            ->map(fn ($records, $month) => [
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'weight' => round((float) $records->avg('average_weight_grams'), 2),
                'records' => $records->count(),
            ])
            ->values();
    }

    private function healthTrend(Carbon $start, Carbon $end): array
    {
        $health = HealthRecord::query()->whereBetween('recorded_date', [$start, $end])->get();
        $mortality = MortalityRecord::query()->whereBetween('mortality_date', [$start, $end])->get();

        return [
            'incidents' => $health->count(),
            'affected' => (int) $health->sum('affected_count'),
            'deaths' => (int) $mortality->sum('count'),
            'monthly' => $mortality->groupBy(fn ($record) => $record->mortality_date->format('Y-m'))
                ->map(fn ($records, $month) => [
                    'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                    'deaths' => (int) $records->sum('count'),
                ])
                ->values(),
        ];
    }

    private function productionTrend(Carbon $start, Carbon $end): Collection
    {
        return Batch::query()
            ->with(['dailyProductionRecords' => fn ($query) => $query->whereBetween('recorded_date', [$start, $end])])
            ->get()
            ->flatMap(fn (Batch $batch) => $batch->dailyProductionRecords->map(fn ($record) => [
                'date' => $record->recorded_date->format('M j'),
                'output' => max(0, (int) $batch->initial_quantity - (int) $record->mortality_heads),
                'batch' => $batch->batch_code,
            ]))
            ->groupBy('date')
            ->map(fn ($records, $label) => [
                'label' => $label,
                'output' => (int) $records->sum('output'),
            ])
            ->values();
    }

    private function salesTrend(Carbon $start, Carbon $end): Collection
    {
        return Sale::valid()
            ->whereBetween('sale_date', [$start, $end])
            ->get()
            ->groupBy(fn ($record) => $record->sale_date->format('Y-m'))
            ->map(fn ($records, $month) => [
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'volume' => (int) $records->sum('heads_sold'),
                'revenue' => round((float) $records->sum('total_amount'), 2),
            ])
            ->values();
    }

    private function dateSeries(Carbon $start, Carbon $end): Collection
    {
        $dates = collect();
        $date = $start->copy()->startOfDay();

        while ($date->lte($end)) {
            $dates->push($date->copy());
            $date->addDay();
        }

        return $dates;
    }
}
