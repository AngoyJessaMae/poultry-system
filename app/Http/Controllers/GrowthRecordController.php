<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrowthRecordRequest;
use App\Models\Batch;
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
        $batches = Batch::where('status', 'active')->get();
        $view = auth()->user()->isManager() ? 'manager.growth-records.create' : 'worker.growth-records.create';
        return view($view, compact('batches'));
    }

    public function store(StoreGrowthRecordRequest $request)
    {
        $this->authorize('create', GrowthRecord::class);
        $data = $request->validated();

        // Calculate growth stage based on the submitted data
        $age = (int) $data['age_days'];
        $weightInKg = (float) $data['average_weight_grams'] / 1000;

        $stage = 'Chick'; // Default stage
        if ($age >= 26 && $weightInKg >= 1.3) {
            $stage = 'Market-Ready';
        } elseif ($age >= 11) {
            $stage = 'Grower';
        }

        $data['growth_stage'] = $stage;
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