<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedingLogRequest;
use App\Models\Batch;
use App\Models\FeedingLog;
use Illuminate\Http\Request;
use App\Traits\ManagesFeedingSchedules;
use Carbon\Carbon;

class FeedingLogController extends Controller
{
    use ManagesFeedingSchedules;

    public function index()
    {
        $this->authorize('viewAny', FeedingLog::class);

        $user = auth()->user();

        // Get feeding schedules
        $batchQuery = Batch::query()->where('status', 'active');
        $batches = $batchQuery->get();
        $schedules = $this->getUpcomingFeedingsForBatches($batches);

        // Get feeding logs
        $logQuery = FeedingLog::with('batch', 'user');
        if ($user->isWorker()) {
            $logQuery->where('user_id', $user->id);
        }
        $feedingLogs = $logQuery->latest()->get();

        $view = $user->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return view($view, compact('schedules', 'feedingLogs'));
    }

    public function create($batch_id)
    {
        $this->authorize('create', FeedingLog::class);
        $batch = Batch::findOrFail($batch_id);
        return view('worker.feeding-logs.create', compact('batch'));
    }

    public function store(Request $request, $batch_id)
    {
        $this->authorize('create', FeedingLog::class);
        $batch = Batch::findOrFail($batch_id);

        $data = $request->validate([
            'feed_type' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'feeding_time' => 'required|date',
        ]);

        $batch->feedingLogs()->create($data + ['user_id' => auth()->id()]);

        return redirect()->route('worker.feeding-logs.index')->with('success', 'Feeding logged successfully.');
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

    public function selectBatch()
    {
        $this->authorize('create', FeedingLog::class);
        $batches = Batch::where('status', 'active')
            ->get();
        return view('worker.feeding-logs.select-batch', compact('batches'));
    }

    public function createForBatch(Request $request)
    {
        $this->authorize('create', FeedingLog::class);
        $batch_id = $request->validate(['batch_id' => 'required|exists:batches,id'])['batch_id'];
        return redirect()->route('worker.batches.feeding-logs.create', $batch_id);
    }


}