<?php

namespace App\Exports;

use App\Models\Batch;
use App\Services\DailyProductionReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DailyProductionReportExport implements FromView
{
    public function __construct(private readonly Batch $batch)
    {
    }

    public function view(): View
    {
        return view('reports.daily-export', [
            'batch' => $this->batch->load('flockman'),
            'rows' => app(DailyProductionReportService::class)->rows($this->batch),
        ]);
    }
}