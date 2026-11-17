<?php

namespace App\Http\Controllers;

use App\Services\FinancialSummaryService;
use App\Services\MonitoringAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request, MonitoringAnalyticsService $analytics, FinancialSummaryService $financial)
    {
        $filters = $request->validate([
            'metric' => ['nullable', 'in:feeding,growth,health,sales,financial'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $metric = $filters['metric'] ?? 'feeding';
        $year = (int) ($filters['year'] ?? now()->year);
        $month = isset($filters['month']) ? (int) $filters['month'] : null;
        $start = Carbon::create($year, $month ?: 1, 1)->startOfMonth();
        $end = $month ? $start->copy()->endOfMonth() : Carbon::create($year, 12, 31)->endOfDay();
        $previousStart = $month ? $start->copy()->subMonth()->startOfMonth() : $start->copy()->subYear();
        $previousEnd = $month ? $previousStart->copy()->endOfMonth() : $previousStart->copy()->endOfYear();

        $current = $analytics->managerOverview($year, $month);
        $previous = $analytics->managerOverview($previousStart->year, $month ? $previousStart->month : null);
        $currentFinancial = $month ? $financial->summarize($year, $month) : $financial->summarizeYear($year);
        $previousFinancial = $month ? $financial->summarize($previousStart->year, $previousStart->month) : $financial->summarizeYear($previousStart->year);

        $definitions = [
            'feeding' => ['title' => 'Feeding Monitoring', 'chart' => 'line', 'description' => 'Track daily feed consumption and compare it with the previous period.', 'rows' => $current['feeding']['daily'], 'key' => 'kg', 'color' => '#f97316', 'value' => $current['feeding']['total_kg'], 'previous' => $previous['feeding']['total_kg'], 'unit' => 'kg', 'empty' => 'No feeding records for this period.'],
            'growth' => ['title' => 'Growth Monitoring', 'chart' => 'dots', 'description' => 'Review average chicken weight measurements over time.', 'rows' => $current['growth'], 'key' => 'weight', 'color' => '#10b981', 'value' => $current['growth']->avg('weight') ?: 0, 'previous' => $previous['growth']->avg('weight') ?: 0, 'unit' => 'g average weight', 'empty' => 'No growth measurements for this period.'],
            'health' => ['title' => 'Health Monitoring', 'chart' => 'pie', 'description' => 'Compare health and mortality records. Chicken losses are shown separately in the summary.', 'rows' => $current['health']['monthly'], 'key' => 'deaths', 'color' => '#f43f5e', 'segments' => [['label' => 'Health records', 'value' => $current['health']['incidents'], 'color' => '#fb7185'], ['label' => 'Mortality records', 'value' => $current['health']['mortality_records'], 'color' => '#be123c']], 'value' => $current['health']['deaths'], 'previous' => $previous['health']['deaths'], 'unit' => 'chickens lost', 'empty' => 'No health or mortality records for this period.'],
            'sales' => ['title' => 'Sales Monitoring', 'chart' => 'line', 'description' => 'Compare completed sales revenue across periods.', 'rows' => $current['sales'], 'key' => 'revenue', 'color' => '#8b5cf6', 'value' => $current['sales']->sum('revenue'), 'previous' => $previous['sales']->sum('revenue'), 'unit' => 'PHP revenue', 'empty' => 'No completed sales for this period.'],
            'financial' => ['title' => 'Financial Monitoring', 'chart' => 'line', 'description' => 'Compare revenue, expenses, and net profit for the selected period.', 'rows' => collect(range(1, 12))->map(fn ($m) => ['label' => Carbon::create($year, $m, 1)->format('M'), 'net' => $financial->summarize($year, $m)['net_profit']]), 'key' => 'net', 'color' => '#f59e0b', 'value' => $currentFinancial['net_profit'], 'previous' => $previousFinancial['net_profit'], 'unit' => 'PHP net profit', 'empty' => 'No financial records for this period.'],
        ];

        $selected = $definitions[$metric];
        $selected['change'] = $selected['value'] - $selected['previous'];
        $selected['change_percent'] = $selected['previous'] == 0 ? null : ($selected['change'] / abs($selected['previous'])) * 100;
        $selected['trend'] = $selected['change'] > 0 ? 'Increase' : ($selected['change'] < 0 ? 'Decrease' : 'No change');

        return view('manager.monitoring.index', compact('definitions', 'selected', 'metric', 'year', 'month', 'start', 'end', 'previousStart', 'previousEnd', 'currentFinancial'));
    }
}
