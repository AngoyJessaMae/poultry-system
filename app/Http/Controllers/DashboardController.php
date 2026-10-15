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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ManagesFeedingSchedules;
    public function managerDashboard()
    {
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
            'sales' => Sale::whereBetween('sale_date', [$trendStart, $trendEnd])->get()->groupBy(fn ($record) => $record->sale_date->toDateString()),
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

        return view('manager.dashboard', compact(
            'pendingWorkers',
            'activeWorkers',
            'generatedReports',
            'activityTrend',
            'activityTrendMax',
            'activityTrendTotal'
        ));
    }

    public function workerDashboard()
    {
        $user = Auth::user();
        $activeBatches = Batch::with('station')->where('status', 'active')->get();

        $recentEntries = collect()
            ->concat($user->feedingLogs()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->growthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->healthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->mortalityRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->sales()->where('created_at', '>=', now()->subHours(24))->get());

        return view('worker.dashboard', compact('activeBatches', 'recentEntries'));
    }
}