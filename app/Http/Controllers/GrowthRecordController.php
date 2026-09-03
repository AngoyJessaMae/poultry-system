<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrowthRecordRequest;
use App\Models\Batch;
use App\Models\GrowthRecord;
use Carbon\Carbon;
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

        $batch = Batch::findOrFail($data['batch_id']);
        $data['age_days'] = $this->ageOnDate($batch, $data['recorded_date']);
        $data['growth_stage'] = $this->growthStage($data['age_days'], $data['average_weight_grams']);
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
        $data = $request->validated();
        $batch = Batch::findOrFail($data['batch_id']);
        $data['age_days'] = $this->ageOnDate($batch, $data['recorded_date']);
        $data['growth_stage'] = $this->growthStage($data['age_days'], $data['average_weight_grams']);
        $growthRecord->update($data);
        $route = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return redirect()->route($route);
    }

    private function ageOnDate(Batch $batch, string $recordedDate): int
    {
        $arrivalDate = Carbon::parse($batch->arrival_date)->startOfDay();
        $recordDate = Carbon::parse($recordedDate)->startOfDay();

        return max(0, $arrivalDate->diffInDays($recordDate, false));
    }

    private function growthStage(int $age, float $averageWeightGrams): string
    {
        $weightInKg = $averageWeightGrams / 1000;

        if ($age >= 26 && $weightInKg >= 1.3) {
            return 'Market-Ready';
        }

        return $age >= 11 ? 'Grower' : 'Chick';
    }

    public function destroy(GrowthRecord $growthRecord)
    {
        $this->authorize('delete', $growthRecord);
        $growthRecord->delete();
        $route = auth()->user()->isManager() ? 'manager.growth-records.index' : 'worker.growth-records.index';
        return redirect()->route($route);
    }
}