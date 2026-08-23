<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrowthRecordRequest;
use App\Models\GrowthRecord;
use Illuminate\Http\Request;

class GrowthRecordController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', GrowthRecord::class);
        $query = GrowthRecord::with('batch', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $growthRecords = $query->get();
        $view = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return view($view, compact('growthRecords'));
    }

    public function create()
    {
        $this->authorize('create', GrowthRecord::class);
        $view = auth()->user()->isManager() ? 'manager.growth-records.create' : 'worker.growth-records.create';
        return view($view);
    }

    public function store(StoreGrowthRecordRequest $request)
    {
        $this->authorize('create', GrowthRecord::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        GrowthRecord::create($data);
        $route = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return redirect()->route($route);
    }

    public function show(GrowthRecord $growthRecord)
    {
        $this->authorize('view', $growthRecord);
        $view = auth()->user()->isManager() ? 'manager.growth-records.show' : 'worker.growth-records.show';
        return view($view, compact('growthRecord'));
    }

    public function edit(GrowthRecord $growthRecord)
    {
        $this->authorize('update', $growthRecord);
        $view = auth()->user()->isManager() ? 'manager.growth-records.edit' : 'worker.growth-records.edit';
        return view($view, compact('growthRecord'));
    }

    public function update(StoreGrowthRecordRequest $request, GrowthRecord $growthRecord)
    {
        $this->authorize('update', $growthRecord);
        $growthRecord->update($request->validated());
        $route = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return redirect()->route($route);
    }

    public function destroy(GrowthRecord $growthRecord)
    {
        $this->authorize('delete', $growthRecord);
        $growthRecord->delete();
        $route = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return redirect()->route($route);
    }
}