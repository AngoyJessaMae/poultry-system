<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Batch;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Sale::class);
        $query = Sale::with('batch', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $sales = $query->get();
        $view = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return view($view, compact('sales'));
    }

    public function create()
    {
        $this->authorize('create', Sale::class);
        $batches = Batch::where('status', 'active')->get();
        $view = auth()->user()->isManager() ? 'manager.sales.create' : 'worker.sales.create';
        return view($view, compact('batches'));
    }

    public function store(StoreSaleRequest $request)
    {
        $this->authorize('create', Sale::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $sale = Sale::create($data);

        // Decrement the batch's current quantity
        $batch = Batch::find($sale->batch_id);
        if ($batch) {
            $batch->decrement('current_quantity', $sale->heads_sold);
        }

        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route);
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        $view = auth()->user()->isManager() ? 'manager.sales.show' : 'worker.sales.show';
        return view($view, compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $this->authorize('update', $sale);
        $view = auth()->user()->isManager() ? 'manager.sales.edit' : 'worker.sales.edit';
        return view($view, compact('sale'));
    }

    public function update(StoreSaleRequest $request, Sale $sale)
    {
        $this->authorize('update', $sale);
        $sale->update($request->validated());
        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route);
    }

    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);
        $sale->delete();
        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route);
    }
}