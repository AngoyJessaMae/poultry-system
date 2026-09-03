<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Models\Report;

class ReportExport implements FromView
{
    protected $filters;
    protected $report;

    public function __construct(array $filters, Report $report)
    {
        $this->filters = $filters;
        $this->report = $report;
    }

    public function view(): View
    {
        $data = $this->getData($this->filters);
        $analytics = $this->getAnalytics($this->filters);
        return view('reports.excel', [
            'data' => $data,
            'analytics' => $analytics,
            'filters' => $this->filters,
            'report' => $this->report,
        ]);
    }

    private function getAnalytics(array $filters)
    {
        // Feed-to-weight ratio
        $totalFeed = FeedingLog::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->whereHas('batch', fn ($batchQuery) => $batchQuery->where('station_id', $id)))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->sum('quantity');

        $feedingRecords = FeedingLog::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->whereHas('batch', fn ($batchQuery) => $batchQuery->where('station_id', $id)))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->orderBy('feeding_time')
            ->get();

        $growthRecords = \App\Models\GrowthRecord::query()
            ->join('batches', 'growth_records.batch_id', '=', 'batches.id')
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('recorded_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('recorded_date', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('batches.station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('growth_records.batch_id', $id))
            ->select('growth_records.*')
            ->orderBy('recorded_date')
            ->get();

        $weightGain = 0;
        if ($growthRecords->count() > 1) {
            $weightGain = ($growthRecords->last()->average_weight_grams - $growthRecords->first()->average_weight_grams) / 1000; // in kg
        }

        $feedToWeightRatio = $weightGain > 0 ? $totalFeed / $weightGain : 0;

        // Mortality rate
        $totalMortality = MortalityRecord::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('mortality_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('mortality_date', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->sum('count');

        $initialQuantity = \App\Models\Batch::query()
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->sum('initial_quantity');

        $mortalityRate = $initialQuantity > 0 ? ($totalMortality / $initialQuantity) * 100 : 0;

        // Total sales
        $totalSales = Sale::query()
            ->join('batches', 'sales.batch_id', '=', 'batches.id')
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('sale_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('sale_date', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('batches.station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('sales.batch_id', $id))
            ->sum('total_amount');

        $baselineProfit = 7000;
        $profitDifference = $baselineProfit > 0 ? (($totalSales - $baselineProfit) / $baselineProfit) * 100 : 0;

        // Worker feeding activity - track which workers did the feedings
        // First get all feeding logs, then aggregate in PHP to avoid MySQL ONLY_FULL_GROUP_BY issues
        $allFeedingLogs = FeedingLog::query()
            ->with('user') // Eager load user relationship
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->get();
        
        // Aggregate manually in PHP to avoid SQL group by issues
        $workerFeedingActivity = $allFeedingLogs->groupBy(function($log) {
            $workerName = $log->user->name ?? 'Unknown Worker';
            $workerId = $log->user->id ?? 0;
            return $workerId . '|' . $workerName;
        })->map(function($logs, $key) {
            [$workerId, $workerName] = explode('|', $key, 2);
            return (object)[
                'worker_id' => $workerId,
                'worker_name' => $workerName,
                'total_feedings' => $logs->count(),
                'total_feed_kg' => $logs->sum('quantity')
            ];
        })->sortByDesc('total_feedings')->values();

        // Additional overall analytics
        $totalFeedingLogs = $feedingRecords->count();
        $totalGrowthRecords = $growthRecords->count();
        $totalMortalityRecords = MortalityRecord::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('mortality_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('mortality_date', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->count();
        $totalSalesRecords = Sale::query()
            ->join('batches', 'sales.batch_id', '=', 'batches.id')
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('sale_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('sale_date', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('batches.station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('sales.batch_id', $id))
            ->count();
        $uniqueWorkers = $workerFeedingActivity->count();

        return [
            'feedToWeightRatio' => $feedToWeightRatio,
            'mortalityRate' => $mortalityRate,
            'totalSales' => $totalSales,
            'profitDifference' => $profitDifference,
            'workerFeedingActivity' => $workerFeedingActivity,
            'totalFeedingLogs' => $totalFeedingLogs,
            'totalGrowthRecords' => $totalGrowthRecords,
            'totalMortalityRecords' => $totalMortalityRecords,
            'totalSalesRecords' => $totalSalesRecords,
            'uniqueWorkers' => $uniqueWorkers,
            'totalFeedUsed' => $totalFeed,
        ];
    }

    private function getData(array $filters)
    {
        $reportType = $filters['report_type'] ?? 'all';
        $query = match ($reportType) {
            'feeding' => FeedingLog::query(),
            'growth' => GrowthRecord::query(),
            'mortality' => MortalityRecord::query(),
            'sales' => Sale::query(),
            default => collect(),
        };

        if ($reportType !== 'all') {
            return $query
                ->when($filters['start_date'] ?? null, function ($q, $date) use ($reportType) {
                    match($reportType) {
                        'feeding' => $q->where('feeding_time', '>=', $date),
                        'growth' => $q->where('recorded_date', '>=', $date),
                        'mortality' => $q->where('mortality_date', '>=', $date),
                        'sales' => $q->where('sale_date', '>=', $date),
                        default => $q->where('created_at', '>=', $date),
                    };
                })
                ->when($filters['end_date'] ?? null, function ($q, $date) use ($reportType) {
                    match($reportType) {
                        'feeding' => $q->where('feeding_time', '<=', $date),
                        'growth' => $q->where('recorded_date', '<=', $date),
                        'mortality' => $q->where('mortality_date', '<=', $date),
                        'sales' => $q->where('sale_date', '<=', $date),
                        default => $q->where('created_at', '<=', $date),
                    };
                })
                ->when($filters['station_id'] ?? null, function ($q, $id) use ($reportType) {
                    match($reportType) {
                        'feeding' => $q->whereHas('batch', fn ($batchQuery) => $batchQuery->where('station_id', $id)),
                        'growth' => $q->where('batches.station_id', $id),
                        'mortality' => $q->where('station_id', $id),
                        'sales' => $q->where('batches.station_id', $id),
                        default => $q->where('station_id', $id),
                    };
                })
                ->when($filters['batch_id'] ?? null, function ($q, $id) use ($reportType) {
                    match($reportType) {
                        'feeding' => $q->where('batch_id', $id),
                        'growth' => $q->where('growth_records.batch_id', $id),
                        'mortality' => $q->where('batch_id', $id),
                        'sales' => $q->where('sales.batch_id', $id),
                        default => $q->where('batch_id', $id),
                    };
                })
                ->get();
        }

        return collect([
            'feeding' => FeedingLog::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('feeding_time', '>=', $date))->get(),
            'growth' => GrowthRecord::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('recorded_date', '>=', $date))->get(),
            'mortality' => MortalityRecord::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('mortality_date', '>=', $date))->get(),
            'sales' => Sale::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('sale_date', '>=', $date))->get(),
        ]);
    }
}