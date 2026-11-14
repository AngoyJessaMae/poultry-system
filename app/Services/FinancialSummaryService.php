<?php

namespace App\Services;

use App\Models\FeedPrice;
use App\Models\FeedingLog;
use App\Models\FinancialAdjustment;
use App\Models\OperationalExpense;
use App\Models\OtherIncome;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialSummaryService
{
    public function summarizeYear(int $year): array
    {
        $monthly = collect(range(1, 12))->map(fn (int $month) => $this->summarize($year, $month));

        return [
            'period' => $year,
            'year' => $year,
            'month' => null,
            'feed_kg' => $monthly->sum('feed_kg'),
            'feed_sacks' => $monthly->sum('feed_sacks'),
            'feed_expense' => $monthly->sum('feed_expense'),
            'sales_revenue' => $monthly->sum('sales_revenue'),
            'other_income' => $monthly->sum('other_income'),
            'other_expenses' => $monthly->sum('other_expenses'),
            'total_expenses' => $monthly->sum('total_expenses'),
            'net_adjustments' => $monthly->sum('net_adjustments'),
            'net_profit' => $monthly->sum('net_profit'),
            'stations' => collect(),
            'batches' => collect(),
            'daily' => collect(),
        ];
    }

    public function summarize(int $year, int $month, ?int $stationId = null, ?int $batchId = null, ?string $feedType = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $logs = FeedingLog::with(['station', 'batch', 'user'])
            ->whereBetween('feeding_time', [$start, $end])
            ->when($stationId, fn ($q) => $q->where('station_id', $stationId))
            ->when($batchId, fn ($q) => $q->where('batch_id', $batchId))
            ->when($feedType, fn ($q) => $q->where('feed_type', $feedType))
            ->orderByDesc('feeding_time')->get();

        $priceCache = [];
        $stationTotals = [];
        $batchTotals = [];
        $daily = [];
        $feedKg = 0.0;
        $feedExpense = 0.0;

        foreach ($logs as $log) {
            $kg = (float) ($log->quantity_kg ?? ((float) $log->quantity / 1000));
            $date = $log->feeding_time->toDateString();
            $price = $this->priceFor($log->feed_type, $log->station_id, $log->feeding_time, $priceCache);
            $expense = $price ? $kg / (float) $price->sack_weight_kg * (float) $price->price_per_sack : 0.0;
            $feedKg += $kg;
            $feedExpense += $expense;

            $stationKey = (string) $log->station_id;
            $batchKey = $stationKey . ':' . $log->batch_id;
            $this->addTotal($stationTotals, $stationKey, $log->station?->station_code ?? 'Unknown', $kg, $expense, $price);
            $this->addTotal($batchTotals, $batchKey, $log->batch?->batch_code ?? 'Unknown', $kg, $expense, $price, $log->station?->station_code);
            $daily[] = [
                'date' => $date, 'station' => $log->station?->station_code ?? 'Unknown',
                'batch' => $log->batch?->batch_code ?? 'Unknown', 'feed_type' => $log->feed_type,
                'quantity_kg' => $kg, 'expense' => $expense, 'worker' => $log->user?->name,
            ];
        }

        $sales = Sale::valid()->whereBetween('sale_date', [$start->toDateString(), $end->toDateString()])
            ->when($stationId, fn ($q) => $q->where('station_id', $stationId))
            ->when($batchId, fn ($q) => $q->where('batch_id', $batchId))->sum('total_amount');
        $income = OtherIncome::valid()->whereBetween('income_date', [$start->toDateString(), $end->toDateString()])->sum('amount');
        $otherExpenses = OperationalExpense::valid()->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->when($stationId, fn ($q) => $q->where(function ($sub) use ($stationId) { $sub->where('station_id', $stationId)->orWhereNull('station_id'); }))
            ->when($batchId, fn ($q) => $q->where(function ($sub) use ($batchId) { $sub->where('batch_id', $batchId)->orWhereNull('batch_id'); }))
            ->sum('amount');
        $adjustments = FinancialAdjustment::valid()->whereBetween('adjustment_date', [$start->toDateString(), $end->toDateString()])->get();
        $netAdjustments = $adjustments->where('adjustment_type', 'increase')->sum('amount') - $adjustments->where('adjustment_type', 'decrease')->sum('amount');
        $totalExpenses = $feedExpense + (float) $otherExpenses;

        return [
            'period' => $start->format('F Y'), 'year' => $year, 'month' => $month,
            'feed_kg' => $feedKg, 'feed_sacks' => $this->sacks($feedKg), 'feed_expense' => $feedExpense,
            'sales_revenue' => (float) $sales, 'other_income' => (float) $income,
            'other_expenses' => (float) $otherExpenses, 'total_expenses' => $totalExpenses,
            'net_adjustments' => (float) $netAdjustments,
            'net_profit' => (float) $sales + (float) $income - $totalExpenses + (float) $netAdjustments,
            'stations' => collect($stationTotals)->sortByDesc('quantity_kg')->values(),
            'batches' => collect($batchTotals)->sortByDesc('quantity_kg')->values(),
            'daily' => collect($daily)->sortByDesc('date')->values(),
        ];
    }

    private function priceFor(string $feedType, int $stationId, Carbon $date, array &$cache): ?FeedPrice
    {
        $key = $stationId . '|' . $feedType . '|' . $date->toDateString();
        if (array_key_exists($key, $cache)) return $cache[$key];
        $period = fn ($query) => $query->whereDate('effective_from', '<=', $date->toDateString())
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date->toDateString()))
            ->latest('effective_from');
        $price = $period(FeedPrice::where('station_id', $stationId)->where('feed_type', $feedType))->first();
        $price ??= $period(FeedPrice::whereNull('station_id')->where('feed_type', $feedType))->first();
        return $cache[$key] = $price;
    }

    private function addTotal(array &$totals, string $key, string $label, float $kg, float $expense, ?FeedPrice $price, ?string $station = null): void
    {
        if (! isset($totals[$key])) $totals[$key] = ['label' => $label, 'station' => $station, 'quantity_kg' => 0.0, 'sacks' => 0.0, 'expense' => 0.0, 'price_per_sack' => $price ? (float) $price->price_per_sack : null];
        $totals[$key]['quantity_kg'] += $kg;
        $totals[$key]['sacks'] += $kg / (float) ($price?->sack_weight_kg ?? 50);
        $totals[$key]['expense'] += $expense;
    }

    private function sacks(float $kg): float
    {
        return $kg / 50;
    }
}
