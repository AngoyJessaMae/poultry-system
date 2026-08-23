<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\FeedingSchedule;
use App\Models\MortalityRecord;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function managerDashboard()
    {
        $totalActiveBatches = Batch::active()->count();
        $todaysMortalityCount = MortalityRecord::whereDate('created_at', today())->sum('count');
        $todaysFeedingLogsCount = FeedingLog::whereDate('created_at', today())->count();
        $monthToDateSalesTotal = Sale::whereMonth('created_at', today()->month)->sum('total_amount');
        $batchesBelowExpected = Batch::where('is_below_expected', true)->get();

        return view('manager.dashboard', compact(
            'totalActiveBatches',
            'todaysMortalityCount',
            'todaysFeedingLogsCount',
            'monthToDateSalesTotal',
            'batchesBelowExpected'
        ));
    }

    public function workerDashboard()
    {
        $user = Auth::user();
        // Temporary fix until we add station assignment to workers - get all stations for now
        $workerStations = \App\Models\Station::pluck('id');

        $missedFeedings = [];
        $pendingFeedingSchedules = collect();
        if ($workerStations->isNotEmpty()) {
            $schedules = FeedingSchedule::whereIn('station_id', $workerStations)->get();

            foreach ($schedules as $schedule) {
                $logExists = FeedingLog::where('station_id', $schedule->station_id)
                    ->whereDate('created_at', today())
                    ->whereTime('created_at', '>=', $schedule->scheduled_time)
                    ->exists();

                if (!$logExists && now()->format('H:i:s') > $schedule->scheduled_time) {
                    $missedFeedings[] = $schedule;
                } elseif (!$logExists) {
                    // Add to pending schedules (not yet due or not logged)
                    $pendingFeedingSchedules->push($schedule);
                }
            }
        }

        $recentEntries = collect()
            ->concat($user->feedingLogs()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->growthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->healthRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->mortalityRecords()->where('created_at', '>=', now()->subHours(24))->get())
            ->concat($user->sales()->where('created_at', '>=', now()->subHours(24))->get());

        return view('worker.dashboard', compact('missedFeedings', 'pendingFeedingSchedules', 'recentEntries'));
    }
}