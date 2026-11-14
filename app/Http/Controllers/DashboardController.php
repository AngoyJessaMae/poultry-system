<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\HealthRecord;
use App\Models\MortalityRecord;
use App\Models\Report;
use App\Models\Sale;
use App\Models\User;
use App\Traits\ManagesFeedingSchedules;
use App\Services\FinancialSummaryService;
use App\Services\MonitoringAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ManagesFeedingSchedules;
    public function managerDashboard(Request $request, MonitoringAnalyticsService $analyticsService, FinancialSummaryService $financialService)
    {
        $filters = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);
        $year = (int) ($filters['year'] ?? now()->year);
        $month = isset($filters['month']) ? (int) $filters['month'] : null;
        $pendingWorkers = User::where('role', 'worker')->where('is_active', false)->count();
        $activeWorkers = User::where('role', 'worker')->where('is_active', true)->count();
        $generatedReports = Report::count();
        $trendStart = now()->subDays(6)->startOfDay();
        $trendEnd = now()->endOfDay();

        $activityByDate = [
            'feeding' => FeedingLog::whereBetween('feeding_time', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->feeding_time->toDateString()),
            'growth' => GrowthRecord::whereBetween('recorded_date', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->recorded_date->toDateString()),
            'health' => HealthRecord::whereBetween('recorded_date', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->recorded_date->toDateString()),
            'mortality' => MortalityRecord::whereBetween('mortality_date', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->mortality_date->toDateString()),
            'sales' => Sale::valid()->whereBetween('sale_date', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->sale_date->toDateString()),
        ];

        $activityTrend = collect(range(0, 6))->map(function (int $offset) use ($trendStart, $activityByDate) {
            $date = $trendStart->copy()->addDays($offset);
            $dateKey = $date->toDateString();
            $counts = collect($activityByDate)->map(fn ($records) => $records->get($dateKey, collect())->count());

            return [
                'label' => $date->format('D'),
                'date' => $date->format('M j'),
                'feeding' => $counts['feeding'],
                'growth' => $counts['growth'],
                'health' => $counts['health'],
                'mortality' => $counts['mortality'],
                'sales' => $counts['sales'],
                'total' => $counts->sum(),
            ];
        });

        $activityTrendMax = max(1, $activityTrend->max('total'));
        $activityTrendTotal = $activityTrend->sum('total');
        $monitoring = $analyticsService->managerOverview($year, $month);
        $financial = $month
            ? $financialService->summarize($year, $month)
            : $financialService->summarizeYear($year);
        $pendingRequests = \App\Models\ManagementRequest::where('status', 'pending')->count();

        return view('manager.dashboard', compact(
            'pendingWorkers',
            'activeWorkers',
            'generatedReports',
            'activityTrend',
            'activityTrendMax',
            'activityTrendTotal',
            'monitoring',
            'financial',
            'pendingRequests'
        ));
    }

    public function workerDashboard()
    {
        $user = Auth::user();
        $activeBatches = Batch::with('station')->where('status', 'active')->orderBy('batch_code')->get();
        $assignedStations = $activeBatches->pluck('station')->filter()->unique('id')->sortBy('station_code');
        $pendingRequests = $user->managementRequests()->where('status', 'pending')->count();

        $recentEntries = collect()
            ->concat($user->feedingLogs()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->growthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->healthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->mortalityRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ;

        return view('worker.dashboard', compact('activeBatches', 'assignedStations', 'pendingRequests', 'recentEntries'));
    }
}
