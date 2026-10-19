<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
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
                $sale = Sale::create($saleData);
                
                // Log the activity
                ActivityLogger::logActivity(auth()->user(), 'create-sale', "Created sale #{$sale->id} for batch {$batch->name} with {$data['heads_sold']} birds sold");
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

}