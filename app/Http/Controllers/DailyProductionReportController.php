<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\DailyProductionRecord;
use App\Models\ProductionStandard;
use App\Services\DailyProductionReportService;
use App\Exports\DailyProductionReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class DailyProductionReportController extends Controller
{
    public function index()
    {
        $batches = Batch::query()->with('flockman')->orderBy('batch_code')->get();

        return view('reports.daily-index', compact('batches'));
    }

    public function show(Batch $batch, DailyProductionReportService $service)
    {
        $batch->load('flockman');
        $rows = $service->rows($batch);
        $standards = ProductionStandard::query()->orderBy('age_days')->get();

        return view('reports.daily', compact('batch', 'rows', 'standards'));
    }

    public function updateHeader(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'arrival_date' => ['required', 'date'],
            'initial_quantity' => ['required', 'integer', 'min:1'],
            'initial_weight_grams' => ['required', 'numeric', 'min:0'],
            'flockman_id' => ['nullable', 'exists:users,id'],
        ]);

        $batch->update($data + ['farm_name' => config('farm.name')]);

        return back()->with('success', 'Production report header saved.');
    }

    public function upsertRow(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'recorded_date' => ['required', 'date', 'after_or_equal:' . $batch->arrival_date->toDateString()],
            'mortality_heads' => ['required', 'integer', 'min:0'],
            'actual_live_weight_grams' => ['nullable', 'numeric', 'min:0'],
            'feed_type' => ['nullable', 'string', 'max:50'],
            'actual_feed_grams' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $batch->dailyProductionRecords()->updateOrCreate(
            ['recorded_date' => $data['recorded_date']],
            $data + ['recorded_by' => auth()->id()]
        );

        return back()->with('success', 'Daily production record saved.');
    }

    public function upsertStandard(Request $request)
    {
        abort_unless(auth()->user()->isManager(), 403);

        $data = $request->validate([
            'age_days' => ['required', 'integer', 'min:1'],
            'mortality_percent' => ['nullable', 'numeric', 'min:0'],
            'live_weight_grams' => ['nullable', 'numeric', 'min:0'],
            'feed_grams' => ['nullable', 'numeric', 'min:0'],
            'fcr' => ['nullable', 'numeric', 'min:0'],
        ]);

        ProductionStandard::updateOrCreate(['age_days' => $data['age_days']], $data);

        return back()->with('success', 'Production standard saved.');
    }

    public function exportPdf(Batch $batch, DailyProductionReportService $service)
    {
        $pdf = Pdf::loadView('reports.daily-export', [
            'batch' => $batch->load('flockman'),
            'rows' => $service->rows($batch),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('daily-production-' . $batch->batch_code . '.pdf');
    }

    public function exportExcel(Batch $batch)
    {
        return Excel::download(new DailyProductionReportExport($batch), 'daily-production-' . $batch->batch_code . '.xlsx');
    }
}