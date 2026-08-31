<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthRecordRequest;
use App\Models\Batch;
use App\Models\HealthRecord;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', HealthRecord::class);
        $query = HealthRecord::with('batch', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $healthRecords = $query->get();
        $view = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return view($view, compact('healthRecords'));
    }

    public function create()
    {
        $this->authorize('create', HealthRecord::class);
        $batches = Batch::where('status', 'active')->get();
        $view = auth()->user()->isManager() ? 'manager.health-records.create' : 'worker.health-records.create';
        return view($view, compact('batches'));
    }

    public function store(StoreHealthRecordRequest $request)
    {
        $this->authorize('create', HealthRecord::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        HealthRecord::create($data);
        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }

    public function show(HealthRecord $healthRecord)
    {
        $this->authorize('view', $healthRecord);
        $view = auth()->user()->isManager() ? 'manager.health-records.show' : 'worker.health-records.show';
        return view($view, compact('healthRecord'));
    }

    public function edit(HealthRecord $healthRecord)
    {
        $this->authorize('update', $healthRecord);
        $view = auth()->user()->isManager() ? 'manager.health-records.edit' : 'worker.health-records.edit';
        return view($view, compact('healthRecord'));
    }

    public function update(StoreHealthRecordRequest $request, HealthRecord $healthRecord)
    {
        $this->authorize('update', $healthRecord);
        $healthRecord->update($request->validated());
        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $this->authorize('delete', $healthRecord);
        $healthRecord->delete();
        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }
}