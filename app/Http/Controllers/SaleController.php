<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Batch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Sale::class);
        $applyFilters = function ($query) use ($request) {
            return $query
                ->when($request->filled('date'), fn ($q) => $q->whereDate('sale_date', $request->date))
                ->when($request->filled('batch_id'), fn ($q) => $q->where('batch_id', $request->integer('batch_id')))
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()));
        };
        $sales = $applyFilters(Sale::with(['batch', 'station', 'user', 'cancelledBy']))->latest('sale_date')->latest('id')->get();
        $revenue = $applyFilters(Sale::valid())->sum('total_amount');
        $batches = Batch::orderBy('batch_code')->get();
        return view('manager.sales.index', compact('sales', 'batches', 'revenue'));
    }

    public function create()
    {
        $this->authorize('create', Sale::class);
        $batches = Batch::where('status', 'active')
            ->whereHas('station', fn ($q) => $q->where('station_code', 'S3'))
            ->with('station')->orderBy('batch_code')->get();
        return view('manager.sales.create', compact('batches'));
    }

    public function store(StoreSaleRequest $request)
    {
        $this->authorize('create', Sale::class);
        try {
            $sale = DB::transaction(fn () => $this->createSale($request->validated()));
        } catch (\Throwable $exception) {
            return back()->withErrors(['batch_id' => $exception->getMessage()])->withInput();
        }
        ActivityLogger::logActivity(auth()->user(), 'create-sale', "Created sale {$sale->transaction_number} for batch {$sale->batch->batch_code}");
        return redirect()->route('manager.sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['batch', 'station', 'user', 'cancelledBy']);
        return view('manager.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $this->authorize('update', $sale);
        return view('manager.sales.edit', compact('sale'));
    }

    public function update(StoreSaleRequest $request, Sale $sale)
    {
        $this->authorize('update', $sale);
        try {
            DB::transaction(function () use ($request, $sale) {
                $lockedSale = Sale::lockForUpdate()->findOrFail($sale->id);
                abort_if($lockedSale->status !== 'completed', 422, 'Only completed sales can be edited.');
                $data = $request->validated();
                $batch = Batch::with('station')->lockForUpdate()->findOrFail($data['batch_id']);
                $this->ensureS3Batch($batch);
                $oldBatch = $batch->id === $lockedSale->batch_id
                    ? $batch
                    : Batch::lockForUpdate()->findOrFail($lockedSale->batch_id);
                if ($batch->id !== $oldBatch->id && $batch->current_quantity < $data['heads_sold']) {
                    abort(422, 'The updated sale exceeds the available chicken quantity.');
                }
                $delta = (int) $data['heads_sold'] - (int) $lockedSale->heads_sold;
                if ($batch->id === $oldBatch->id && $delta > 0 && $batch->current_quantity < $delta) {
                    abort(422, 'The updated sale exceeds the available chicken quantity.');
                }
                if ($batch->id !== $oldBatch->id) {
                    $oldBatch->increment('current_quantity', $lockedSale->heads_sold);
                    $batch->decrement('current_quantity', $data['heads_sold']);
                } elseif ($delta > 0) {
                    $batch->decrement('current_quantity', $delta);
                } elseif ($delta < 0) {
                    $batch->increment('current_quantity', abs($delta));
                }
                $lockedSale->update([
                    'batch_id' => $batch->id, 'station_id' => $batch->station_id,
                    'sale_date' => $data['sale_date'], 'heads_sold' => $data['heads_sold'],
                    'total_weight_kg' => $data['total_weight_kg'], 'price_per_kg' => $data['price_per_kg'],
                    'total_amount' => round($data['heads_sold'] * $data['price_per_kg'], 2),
                    'buyer_name' => $data['buyer_name'] ?? null,
                ]);
            });
        } catch (\Throwable $exception) {
            return back()->withErrors(['batch_id' => $exception->getMessage()])->withInput();
        }
        return redirect()->route('manager.sales.index')->with('success', 'Sale updated successfully.');
    }

    public function cancel(Request $request, Sale $sale)
    {
        $this->authorize('cancel', $sale);
        $data = $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);
        try {
            DB::transaction(function () use ($sale, $data) {
                $lockedSale = Sale::lockForUpdate()->findOrFail($sale->id);
                abort_if($lockedSale->status !== 'completed', 422, 'This sale has already been cancelled or voided.');
                $batch = Batch::lockForUpdate()->findOrFail($lockedSale->batch_id);
                $batch->increment('current_quantity', $lockedSale->heads_sold);
                $lockedSale->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => auth()->id(), 'cancellation_reason' => $data['cancellation_reason']]);
            });
        } catch (\Throwable $exception) {
            return back()->withErrors(['cancellation_reason' => $exception->getMessage()])->withInput();
        }
        ActivityLogger::logActivity(auth()->user(), 'cancel-sale', "Cancelled sale {$sale->transaction_number}");
        return redirect()->route('manager.sales.index')->with('success', 'Sale cancelled and quantity restored.');
    }

    private function createSale(array $data): Sale
    {
        $batch = Batch::with('station')->lockForUpdate()->findOrFail($data['batch_id']);
        $this->ensureS3Batch($batch);
        if ((int) $data['heads_sold'] > (int) $batch->current_quantity) {
            abort(422, 'The sale exceeds the available chicken quantity.');
        }
        $batch->decrement('current_quantity', $data['heads_sold']);
        return Sale::create([
            'transaction_number' => 'SALE-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
            'batch_id' => $batch->id, 'station_id' => $batch->station_id, 'user_id' => auth()->id(),
            'sale_date' => $data['sale_date'], 'heads_sold' => $data['heads_sold'],
            'total_weight_kg' => $data['total_weight_kg'], 'price_per_kg' => $data['price_per_kg'],
            'total_amount' => round($data['heads_sold'] * $data['price_per_kg'], 2),
            'buyer_name' => $data['buyer_name'] ?? null, 'status' => 'completed',
        ]);
    }

    private function ensureS3Batch(Batch $batch): void
    {
        abort_unless($batch->status === 'active', 422, 'Only active batches can be sold.');
        abort_unless($batch->station && $batch->station->station_code === 'S3', 422, 'Only batches currently assigned to S3 can be sold.');
    }
}
