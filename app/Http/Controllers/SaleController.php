<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Batch;
use App\Models\Sale;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

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

        try {
            DB::transaction(function () use ($data) {
                $batch = Batch::lockForUpdate()->find($data['batch_id']);

                if (!$batch || $batch->current_quantity < $data['heads_sold']) {
                    throw new \Exception('Not enough birds in the batch for this sale.');
                }

                $batch->decrement('current_quantity', $data['heads_sold']);

                $saleData = $data;
                $saleData['user_id'] = auth()->id();
                Sale::create($saleData);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['heads_sold' => $e->getMessage()]);
        }

        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route)->with('success', 'Sale recorded successfully.');
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

        $originalHeadsSold = $sale->heads_sold;
        $newHeadsSold = $request->validated()['heads_sold'];
        $difference = $newHeadsSold - $originalHeadsSold;

        try {
            DB::transaction(function () use ($sale, $request, $difference) {
                $batch = Batch::lockForUpdate()->find($sale->batch_id);

                if ($difference > 0 && $batch->current_quantity < $difference) {
                    throw new \Exception('Not enough birds in the batch to update this sale.');
                }

                $batch->decrement('current_quantity', $difference);

                $sale->update($request->validated());
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['heads_sold' => $e->getMessage()]);
        }

        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route)->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $this->authorize('delete', $sale);

        try {
            DB::transaction(function () use ($sale) {
                $batch = Batch::lockForUpdate()->find($sale->batch_id);
                $batch->increment('current_quantity', $sale->heads_sold);
                $sale->delete();
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the sale.']);
        }

        $route = auth()->user()->isManager() ? 'manager.sales.index' : 'worker.sales.index';
        return redirect()->route($route)->with('success', 'Sale deleted successfully.');
    }
}