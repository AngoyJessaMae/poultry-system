<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBatchRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;
use App\Models\Station;
use App\Models\ManagementRequest;
use App\Models\User;
use App\Notifications\ManagementRequestSubmitted;
use App\Helpers\ActivityLogger;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $data['batch_code'] = strtoupper(trim($data['batch_code']));
        $duplicate = Batch::whereRaw('LOWER(batch_code) = ?', [strtolower($data['batch_code'])])->exists();
        if ($duplicate) {
            return back()->withErrors(['batch_code' => 'A batch with this identifier already exists.'])->withInput();
        }
        $pendingDuplicate = ManagementRequest::where('request_type', 'batch')
            ->where('status', 'pending')
            ->get()
            ->contains(fn (ManagementRequest $item) => strtolower($item->payload['batch_code'] ?? '') === strtolower($data['batch_code']));
        if ($pendingDuplicate) {
            return back()->withErrors(['batch_code' => 'A request for this batch identifier is already pending approval.'])->withInput();
        }

        if ($data['status'] === 'active') {
            $station = Station::findOrFail($data['station_id']);
            $occupiedQuantity = $station->batches()->where('status', 'active')->sum('current_quantity');

            if ($occupiedQuantity + $data['initial_quantity'] > $station->capacity) {
                return back()->withErrors([
                    'station_id' => "The selected station has only {$station->available_capacity} bird spaces remaining, so this batch cannot be added.",
                ])->withInput();
            }
        }

        $managementRequest = ManagementRequest::create([
            'request_type' => 'batch',
            'request_key' => strtolower($data['batch_code']),
            'requested_by' => auth()->id(),
            'status' => 'pending',
            'payload' => $data,
        ]);
        User::where('role', 'manager')->get()->each(fn (User $manager) => $manager->notify(new ManagementRequestSubmitted($managementRequest)));
        ActivityLogger::logActivity(auth()->user(), 'submit-management-request', "Submitted batch request #{$managementRequest->id}");

        return redirect()->route('worker.management-requests.index')->with('success', 'Batch request submitted for manager approval.');
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

        if ($request->hasAny(['initial_quantity', 'current_quantity'])) {
            abort(403, 'Batch quantities can only be changed through authorized quantity transactions.');
        }

        $data = $request->validated();
        try {
            DB::transaction(function () use ($batch, $data) {
                $lockedBatch = Batch::lockForUpdate()->findOrFail($batch->id);
                $station = Station::lockForUpdate()->findOrFail($data['station_id'] ?? $lockedBatch->station_id);
                $status = $data['status'] ?? $lockedBatch->status;

                if ($status === 'active') {
                    $occupiedQuantity = $station->batches()
                        ->where('status', 'active')
                        ->where('batches.id', '!=', $lockedBatch->id)
                        ->sum('current_quantity');

                    if ($occupiedQuantity + $lockedBatch->current_quantity > $station->capacity) {
                        throw new \RuntimeException("The selected station does not have enough capacity for this batch.");
                    }
                }

                $lockedBatch->update($data);
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['station_id' => $exception->getMessage()])->withInput();
        }
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
