<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedingLogRequest;
use App\Models\FeedingLog;
use Illuminate\Http\Request;

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
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return view($view, compact('feedingLogs'));
    }

    public function create()
    {
        $this->authorize('create', FeedingLog::class);
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.create' : 'worker.feeding-logs.create';
        return view($view);
    }

    public function store(StoreFeedingLogRequest $request)
    {
        $this->authorize('create', FeedingLog::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
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
}