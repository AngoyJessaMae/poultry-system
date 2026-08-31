<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
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
        $batch->update($request->validated());
        $route = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';
        return redirect()->route($route);
    }

    public function destroy(Batch $batch)
    {
        $this->authorize('delete', $batch);
        $batch->delete();
        $route = auth()->user()->isManager() ? 'manager.batches.index' : 'worker.batches.index';
        return redirect()->route($route);
    }
}