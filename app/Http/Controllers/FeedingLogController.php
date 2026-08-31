<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedingLogRequest;
use App\Models\Batch;
use App\Models\FeedingLog;
use Illuminate\Http\Request;
use App\Models\FeedingSchedule;
use Carbon\Carbon;

class FeedingLogController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', FeedingLog::class);
        $query = FeedingLog::with('batch', 'station', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $feedingLogs = $query->get();

        // Get today's pending feeding schedules for the worker
        $pendingFeedingSchedules = [];
        if (auth()->user()->isWorker()) {
            $today = Carbon::today();
            $loggedSchedules = FeedingLog::where('user_id', auth()->id())
                ->whereDate('fed_at', $today)
                ->whereNotNull('feeding_schedule_id')
                ->pluck('feeding_schedule_id')
                ->all();

            $pendingFeedingSchedules = FeedingSchedule::whereHas('batch', function ($q) {
                $q->where('status', 'active');
            })
            ->whereNotIn('id', $loggedSchedules)
            ->get();
        }

        $view = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return view($view, compact('feedingLogs', 'pendingFeedingSchedules'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', FeedingLog::class);
        $batches = Batch::where('status', 'active')->get();
        $selectedBatchId = $request->query('batch_id');
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.create' : 'worker.feeding-logs.create';
        return view($view, compact('batches', 'selectedBatchId'));
    }

    public function store(StoreFeedingLogRequest $request)
    {
        $this->authorize('create', FeedingLog::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $batch = Batch::find($data['batch_id']);
        $data['station_id'] = $batch->station_id;
        FeedingLog::create($data);
        $route = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return redirect()->route($route);
    }

    public function show(FeedingLog $feedingLog)
    {
        $this->authorize('view', $feedingLog);
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.show' : 'worker.feeding-logs.show';
        return view($view, compact('feedingLog'));
    }

    public function edit(FeedingLog $feedingLog)
    {
        $this->authorize('update', $feedingLog);
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.edit' : 'worker.feeding-logs.edit';
        return view($view, compact('feedingLog'));
    }

    public function update(StoreFeedingLogRequest $request, FeedingLog $feedingLog)
    {
        $this->authorize('update', $feedingLog);
        $feedingLog->update($request->validated());
        $route = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return redirect()->route($route);
    }

    public function destroy(FeedingLog $feedingLog)
    {
        $this->authorize('delete', $feedingLog);
        $feedingLog->delete();
        $route = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return redirect()->route($route);
    }

    public function storeFromSchedule(Request $request)
    {
        $this->authorize('create', FeedingLog::class);

        $request->validate([
            'feeding_schedule_id' => 'required|exists:feeding_schedules,id',
            'feed_type' => 'required|string',
            'quantity_kg' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $schedule = \App\Models\FeedingSchedule::find($request->feeding_schedule_id);
        $batch = $schedule->batch;

        FeedingLog::create([
            'user_id' => auth()->id(),
            'batch_id' => $batch->id,
            'station_id' => $batch->station_id,
            'feeding_schedule_id' => $schedule->id,
            'feeding_time_slot' => $schedule->scheduled_time,
            'feed_type' => $request->feed_type,
            'quantity_kg' => $request->quantity_kg,
            'fed_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('worker.dashboard')->with('success', 'Feeding logged successfully.');
    }
}