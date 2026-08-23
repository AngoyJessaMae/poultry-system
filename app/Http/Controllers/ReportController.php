<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $stations = Station::all();
        $batches = Batch::all();

        $filters = $request->only(['start_date', 'end_date', 'station_id', 'batch_id', 'report_type']);

        $analytics = $this->getAnalytics($filters);

        return view('manager.reports.index', compact('stations', 'batches', 'filters', 'analytics'));
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'station_id', 'batch_id', 'report_type']);
        $analytics = $this->getAnalytics($filters);
        $data = $this->getData($filters);

        $pdf = Pdf::loadView('reports.pdf', compact('analytics', 'data', 'filters'));
        return $pdf->download('report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'station_id', 'batch_id', 'report_type']);
        return Excel::download(new ReportExport($filters), 'report.xlsx');
    }

    private function getAnalytics(array $filters)
    {
        // Feed-to-weight ratio
        $totalFeed = FeedingLog::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('station_id', $id))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->sum('quantity_kg');

        $growthRecords = GrowthRecord::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->orderBy('created_at')
            ->get();

        $weightGain = 0;
        if ($growthRecords->count() > 1) {
            $weightGain = ($growthRecords->last()->average_weight_grams - $growthRecords->first()->average_weight_grams) / 1000; // in kg
        }

        $feedToWeightRatio = $weightGain > 0 ? $totalFeed / $weightGain : 0;

        // Mortality rate
        $totalMortality = MortalityRecord::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->whereHas('batch', fn($q) => $q->where('id', $id)))
            ->sum('count');

        $initialQuantity = Batch::query()
            ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->sum('initial_quantity');

        $mortalityRate = $initialQuantity > 0 ? ($totalMortality / $initialQuantity) * 100 : 0;

        // Monthly profit
        $totalSales = Sale::query()
            ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
            ->sum('total_amount');

        $baselineProfit = 7000;
        $profitDifference = $baselineProfit > 0 ? (($totalSales - $baselineProfit) / $baselineProfit) * 100 : 0;

        return [
            'feedToWeightRatio' => $feedToWeightRatio,
            'mortalityRate' => $mortalityRate,
            'totalSales' => $totalSales,
            'profitDifference' => $profitDifference,
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
                ->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
                ->when($filters['end_date'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
                ->when($filters['station_id'] ?? null, fn ($q, $id) => $q->where('station_id', $id))
                ->when($filters['batch_id'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
                ->get();
        }

        return collect([
            'feeding' => FeedingLog::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))->get(),
            'growth' => GrowthRecord::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))->get(),
            'mortality' => MortalityRecord::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))->get(),
            'sales' => Sale::query()->when($filters['start_date'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))->get(),
        ]);
    }
}