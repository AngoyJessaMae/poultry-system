<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;

class ReportExport implements FromView
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $data = $this->getData($this->filters);
        return view('reports.excel', [
            'data' => $data
        ]);
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