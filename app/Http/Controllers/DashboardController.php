<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Report;
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

        return view('manager.dashboard', compact('pendingWorkers', 'activeWorkers', 'generatedReports'));
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