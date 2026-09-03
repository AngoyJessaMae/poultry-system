<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMortalityRecordRequest;
use App\Models\MortalityRecord;
use Illuminate\Http\Request;

use App\Models\Batch;

class MortalityRecordController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', MortalityRecord::class);
        $query = MortalityRecord::with('batch', 'station', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $mortalityRecords = $query->get();
        $view = auth()->user()->isManager() ? 'manager.mortality-records.index' : 'worker.mortality-records.index';
        return view($view, compact('mortalityRecords'));
    }

    public function create()
    {
        $this->authorize('create', MortalityRecord::class);
        $batches = Batch::where('status', 'active')->get();
        $view = auth()->user()->isManager() ? 'manager.mortality-records.create' : 'worker.mortality-records.create';
        return view($view, compact('batches'));
    }

    public function store(StoreMortalityRecordRequest $request)
    {
        $this->authorize('create', MortalityRecord::class);

        $data = $request->validated();
        $batch = Batch::findOrFail($data['batch_id']);

        if ($batch->current_quantity < $data['count']) {
            return back()->withErrors(['count' => 'Mortality count cannot be greater than the current batch quantity.'])->withInput();
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $batch) {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['station_id'] = $batch->station_id;
            $mortalityRecord = MortalityRecord::create($data);

            // The MortalityRecordObserver handles decrementing the batch quantity.

            // Check for high mortality rate
            $totalMortalities = $batch->mortalityRecords()->sum('count');
            $mortalityRate = ($totalMortalities / $batch->initial_quantity) * 100;

            if ($mortalityRate > 10) {
                $batch->update([
                    'is_below_expected' => true,
                    'below_expected_reason' => 'High Mortality Rate',
                ]);
            }
        });

        $route = auth()->user()->isManager() ? 'manager.mortality-records.index' : 'worker.mortality-records.index';
        return redirect()->route($route);
    }

    public function show(MortalityRecord $mortalityRecord)
    {
        $this->authorize('view', $mortalityRecord);
        $view = auth()->user()->isManager() ? 'manager.mortality-records.show' : 'worker.mortality-records.show';
        return view($view, compact('mortalityRecord'));
    }

    public function edit(MortalityRecord $mortalityRecord)
    {
        $this->authorize('update', $mortalityRecord);
        $view = auth()->user()->isManager() ? 'manager.mortality-records.edit' : 'worker.mortality-records.edit';
        return view($view, compact('mortalityRecord'));
    }

    public function update(StoreMortalityRecordRequest $request, MortalityRecord $mortalityRecord)
    {
        $this->authorize('update', $mortalityRecord);
        $data = $request->validated();
        $batch = Batch::findOrFail($data['batch_id']);
        $availableQuantity = $batch->current_quantity;

        if ((int) $mortalityRecord->batch_id === (int) $batch->id) {
            $availableQuantity += $mortalityRecord->count;
        }

        if ($availableQuantity < $data['count']) {
            return back()->withErrors(['count' => 'Mortality count cannot be greater than the current batch quantity.'])->withInput();
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($mortalityRecord, $data) {
            $mortalityRecord->update($data);
        });
        $route = auth()->user()->isManager() ? 'manager.mortality-records.index' : 'worker.mortality-records.index';
        return redirect()->route($route);
    }

    public function destroy(MortalityRecord $mortalityRecord)
    {
        $this->authorize('delete', $mortalityRecord);
        $mortalityRecord->delete();
        $route = auth()->user()->isManager() ? 'manager.mortality-records.index' : 'worker.mortality-records.index';
        return redirect()->route($route);
    }
}