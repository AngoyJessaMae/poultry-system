<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\Station;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Batch::class);
        $query = Batch::with('station', 'creator');
        $batches = $query->get();
        $view = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';
        return view($view, compact('batches'));
    }

    public function create()
    {
        $this->authorize('create', Batch::class);
        $view = auth()->user()->isManager() ? 'manager.batches.create' : 'worker.batches.create';
        return view($view);
    }

    public function store(StoreBatchRequest $request)
    {
        $this->authorize('create', Batch::class);
        $data = $request->validated();

        if ($data['status'] === 'active') {
            $station = Station::findOrFail($data['station_id']);
            $occupiedQuantity = $station->batches()->where('status', 'active')->sum('current_quantity');

            if ($occupiedQuantity + $data['initial_quantity'] > $station->capacity) {
                return back()->withErrors([
                    'station_id' => "The selected station has only {$station->available_capacity} bird spaces remaining, so this batch cannot be added.",
                ])->withInput();
            }
        }

        $data['created_by'] = auth()->id();
        $data['current_quantity'] = $data['initial_quantity'];
        Batch::create($data);
        $route = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';
        return redirect()->route($route);
    }

    public function show(Batch $batch)
    {
        $this->authorize('view', $batch);
        $view = auth()->user()->isManager() ? 'manager.batches.show' : 'worker.batches.show';
        return view($view, compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $this->authorize('update', $batch);
        $view = auth()->user()->isManager() ? 'manager.batches.edit' : 'worker.batches.edit';
        return view($view, compact('batch'));
    }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        $this->authorize('update', $batch);
        $data = $request->validated();
        $station = Station::findOrFail($data['station_id'] ?? $batch->station_id);
        $status = $data['status'] ?? $batch->status;
        $currentQuantity = $data['current_quantity'] ?? $batch->current_quantity;

        if ($status === 'active') {
            $occupiedQuantity = $station->batches()
                ->where('status', 'active')
                ->where('batches.id', '!=', $batch->id)
                ->sum('current_quantity');

            if ($occupiedQuantity + $currentQuantity > $station->capacity) {
                return back()->withErrors([
                    'station_id' => "The selected station has only {$station->available_capacity} bird spaces remaining, so this batch cannot be assigned there.",
                ])->withInput();
            }
        }

        $batch->update($data);
        $route = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';
        return redirect()->route($route);
    }

    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $route = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';

        $dependencies = [
            'feeding logs' => $batch->feedingLogs()->exists(),
            'growth records' => $batch->growthRecords()->exists(),
            'health records' => $batch->healthRecords()->exists(),
            'mortality records' => $batch->mortalityRecords()->exists(),
            'sales' => $batch->sales()->exists(),
            'movement records' => $batch->movements()->exists(),
        ];

        $usedBy = array_keys(array_filter($dependencies));
        if ($usedBy !== []) {
            return redirect()->route($route)->withErrors([
                'batch' => 'This batch cannot be deleted while it has associated records: ' . implode(', ', $usedBy) . '. Preserve or remove those records first.',
            ]);
        }

        try {
            $batch->delete();
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }

            return redirect()->route($route)->withErrors([
                'batch' => 'This batch cannot be deleted because it is still being used by another record.',
            ]);
        }

        return redirect()->route($route);
    }
}