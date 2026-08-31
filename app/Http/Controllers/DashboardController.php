<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\FeedingSchedule;
use App\Models\MortalityRecord;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $workerBatches = Batch::active()->pluck('id'); // Get all active batches for now

        $missedFeedings = [];
        $pendingFeedingSchedules = collect();

        if ($workerBatches->isNotEmpty()) {
            $schedules = FeedingSchedule::whereIn('batch_id', $workerBatches)
                ->whereHas('batch', function ($query) {
                    $query->where('feeding_method', '!=', 'unlimited');
                })
                ->with('batch.station') // Eager load batch and station
                ->get();

            foreach ($schedules as $schedule) {
                $logExists = FeedingLog::where('feeding_schedule_id', $schedule->id)
                    ->whereDate('created_at', today())
                    ->exists();

                if (!$logExists && now()->format('H:i:s') > $schedule->scheduled_time) {
                    $missedFeedings[] = $schedule;
                } elseif (!$logExists) {
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