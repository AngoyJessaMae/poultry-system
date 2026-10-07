<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Get today's active workers who are currently logged in
        $today = Carbon::today();
        $activeWorkers = User::where('role', 'worker')
            ->whereHas('activityLogs', function ($query) use ($today) {
                $query->whereDate('login_at', $today)
                    ->whereNull('logout_at')
                    ->where('activity_type', 'login');
            })
            ->with('currentSession')
            ->get();

        // Get all workers who used the system today (logged in at some point)
        $todayUsers = User::where('role', 'worker')
            ->whereHas('activityLogs', function ($query) use ($today) {
                $query->whereDate('login_at', $today)
                    ->where('activity_type', 'login');
            })
            ->with(['activityLogs' => function ($query) use ($today) {
                $query->whereDate('login_at', $today)
                    ->where('activity_type', 'login')
                    ->orderBy('login_at', 'desc');
            }])
            ->get();

        return view('manager.activity-logs.index', compact('activeWorkers', 'todayUsers'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        // Get today's activities for this user
        $today = Carbon::today();
        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get login sessions for today
        $loginSessions = ActivityLog::where('user_id', $user->id)
            ->where('activity_type', 'login')
            ->whereDate('login_at', $today)
            ->orderBy('login_at', 'desc')
            ->get();

        return view('manager.activity-logs.show', compact('user', 'activityLogs', 'loginSessions'));
    }

    public function history(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $userId = $request->user_id;

        $query = ActivityLog::where('activity_type', 'login')
            ->whereDate('login_at', $date);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $logs = $query->with('user')
            ->orderBy('login_at', 'desc')
            ->paginate(15);

        $workers = User::where('role', 'worker')->where('is_active', true)->get();

        return view('manager.activity-logs.history', compact('logs', 'date', 'workers', 'userId'));
    }
}