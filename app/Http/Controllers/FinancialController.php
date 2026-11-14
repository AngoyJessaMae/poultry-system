<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedPrice;
use App\Models\FinancialAdjustment;
use App\Models\OperationalExpense;
use App\Models\OtherIncome;
use App\Models\Station;
use App\Services\FinancialSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function index(Request $request, FinancialSummaryService $service)
    {
        $period = $request->validate(['month' => ['nullable', 'integer', 'between:1,12'], 'year' => ['nullable', 'integer', 'min:2000', 'max:2100'], 'station_id' => ['nullable', 'integer', 'exists:stations,id'], 'batch_id' => ['nullable', 'integer', 'exists:batches,id'], 'feed_type' => ['nullable', 'string', 'max:100']]);
        $month = (int) ($period['month'] ?? now()->month);
        $year = (int) ($period['year'] ?? now()->year);
        $summary = $service->summarize($year, $month, $period['station_id'] ?? null, $period['batch_id'] ?? null, $period['feed_type'] ?? null);
        $stations = Station::whereIn('station_code', ['S1', 'S2', 'S3'])->orderBy('station_code')->get();
        $batches = Batch::orderBy('batch_code')->get();
        $feedTypes = FeedPrice::query()->select('feed_type')->distinct()->orderBy('feed_type')->pluck('feed_type');
        $prices = FeedPrice::with(['creator', 'station'])->latest('effective_from')->latest('id')->get();
        $expenses = OperationalExpense::with(['station', 'batch', 'creator'])->latest('expense_date')->latest('id')->limit(25)->get();
        $incomes = OtherIncome::with('creator')->latest('income_date')->latest('id')->limit(25)->get();
        $adjustments = FinancialAdjustment::with('creator')->latest('adjustment_date')->latest('id')->limit(25)->get();
        return view('manager.financial.index', compact('summary', 'stations', 'batches', 'feedTypes', 'prices', 'expenses', 'incomes', 'adjustments', 'period'));
    }

    public function storePrice(Request $request)
    {
        $data = $request->validate(['station_id' => ['nullable', 'integer', 'exists:stations,id'], 'feed_type' => ['required', 'string', 'max:100'], 'price_per_sack' => ['required', 'numeric', 'gt:0'], 'sack_weight_kg' => ['required', 'numeric', 'gt:0'], 'effective_from' => ['required', 'date'], 'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from']]);
        DB::transaction(function () use ($data) {
            $stationId = $data['station_id'] ?? null;
            $priceQuery = FeedPrice::where('feed_type', $data['feed_type'])->whereNull('effective_to')->whereDate('effective_from', '<', $data['effective_from']);
            $stationId
                ? $priceQuery->where('station_id', $stationId)
                : $priceQuery->whereNull('station_id');
            $priceQuery->update(['effective_to' => Carbon::parse($data['effective_from'])->subDay()->toDateString()]);
            FeedPrice::create($data + ['created_by' => auth()->id()]);
        });
        return back()->with('success', 'Feed price saved with its effective period.');
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate(['description' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:50'], 'amount' => ['required', 'numeric', 'gt:0'], 'quantity' => ['nullable', 'numeric', 'gt:0'], 'unit' => ['nullable', 'string', 'max:30'], 'expense_date' => ['required', 'date'], 'allocation_type' => ['required', 'in:overall,station,batch'], 'station_id' => ['nullable', 'exists:stations,id'], 'batch_id' => ['nullable', 'exists:batches,id'], 'notes' => ['nullable', 'string']]);
        abort_if($data['allocation_type'] === 'station' && empty($data['station_id']), 422, 'A station is required for station allocation.');
        abort_if($data['allocation_type'] === 'batch' && empty($data['batch_id']), 422, 'A batch is required for batch allocation.');
        OperationalExpense::create($data + ['created_by' => auth()->id(), 'status' => 'active']);
        return back()->with('success', 'Operational expense recorded.');
    }

    public function storeIncome(Request $request)
    {
        $data = $request->validate(['description' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:50'], 'amount' => ['required', 'numeric', 'gt:0'], 'income_date' => ['required', 'date'], 'notes' => ['nullable', 'string']]);
        OtherIncome::create($data + ['created_by' => auth()->id(), 'status' => 'active']);
        return back()->with('success', 'Other income recorded.');
    }

    public function storeAdjustment(Request $request)
    {
        $data = $request->validate(['adjustment_type' => ['required', 'in:increase,decrease'], 'amount' => ['required', 'numeric', 'gt:0'], 'reason' => ['required', 'string', 'max:1000'], 'adjustment_date' => ['required', 'date']]);
        FinancialAdjustment::create($data + ['created_by' => auth()->id(), 'status' => 'active']);
        return back()->with('success', 'Financial adjustment recorded.');
    }

    public function cancelExpense(Request $request, OperationalExpense $expense)
    {
        $data = $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);
        abort_if($expense->status !== 'active', 422, 'This expense is already cancelled.');
        $expense->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => auth()->id(), 'cancellation_reason' => $data['cancellation_reason']]);
        return back()->with('success', 'Expense cancelled.');
    }

    public function cancelIncome(Request $request, OtherIncome $income)
    {
        $data = $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);
        abort_if($income->status !== 'active', 422, 'This income is already cancelled.');
        $income->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => auth()->id(), 'cancellation_reason' => $data['cancellation_reason']]);
        return back()->with('success', 'Income cancelled.');
    }
}
