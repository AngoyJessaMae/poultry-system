<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthRecordRequest;
use App\Models\Batch;
use App\Models\MortalityRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\HealthRecord;
use App\Models\HealthRecordHistory;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', HealthRecord::class);
        $query = HealthRecord::with('batch', 'user');
        if (auth()->user()->isWorker()) {
            $query->where('user_id', auth()->id());
        }
        $healthRecords = $query->get();
        $view = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return view($view, compact('healthRecords'));
    }

    public function create()
    {
        $this->authorize('create', HealthRecord::class);
        $batches = Batch::where('status', 'active')->get();
        $view = auth()->user()->isManager() ? 'manager.health-records.create' : 'worker.health-records.create';
        return view($view, compact('batches'));
    }

    public function store(StoreHealthRecordRequest $request)
    {
        $this->authorize('create', HealthRecord::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        DB::transaction(function () use ($data) {
            $healthRecord = HealthRecord::create($data);
            $this->syncMortalityRecord($healthRecord);
            $this->recordHistory($healthRecord);
        });

        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }

    public function show(HealthRecord $healthRecord)
    {
        $this->authorize('view', $healthRecord);
        $healthRecord->load(['batch', 'user', 'history.user']);
        $view = auth()->user()->isManager() ? 'manager.health-records.show' : 'worker.health-records.show';
        return view($view, compact('healthRecord'));
    }

    public function edit(HealthRecord $healthRecord)
    {
        $this->authorize('update', $healthRecord);
        $view = auth()->user()->isManager() ? 'manager.health-records.edit' : 'worker.health-records.edit';
        return view($view, compact('healthRecord'));
    }

    public function update(StoreHealthRecordRequest $request, HealthRecord $healthRecord)
    {
        $this->authorize('update', $healthRecord);
        $data = $request->validated();

        DB::transaction(function () use ($healthRecord, $data) {
            $healthRecord->update($data);
            $this->syncMortalityRecord($healthRecord->fresh(['batch', 'mortalityRecord']));
            $this->recordHistory($healthRecord->fresh());
        });

        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }

    public function destroy(HealthRecord $healthRecord)
    {
        $this->authorize('delete', $healthRecord);

        DB::transaction(function () use ($healthRecord) {
            $healthRecord->load('mortalityRecord');
            $healthRecord->mortalityRecord?->delete();
            $healthRecord->delete();
        });

        $route = auth()->user()->isManager() ? 'manager.health-records.index' : 'worker.health-records.index';
        return redirect()->route($route);
    }

    private function syncMortalityRecord(HealthRecord $healthRecord): void
    {
        $healthRecord->loadMissing('batch', 'mortalityRecord');
        $deadCount = (int) $healthRecord->dead_count;
        $mortalityRecord = $healthRecord->mortalityRecord;

        if ($deadCount === 0) {
            $mortalityRecord?->delete();
            if ($healthRecord->mortality_record_id !== null) {
                $healthRecord->update(['mortality_record_id' => null]);
            }
            return;
        }

        $availableQuantity = $healthRecord->batch->current_quantity;
        if ($mortalityRecord && (int) $mortalityRecord->batch_id === (int) $healthRecord->batch_id) {
            $availableQuantity += $mortalityRecord->count;
        }

        if ($availableQuantity < $deadCount) {
            throw ValidationException::withMessages([
                'dead_count' => 'The number of dead chickens cannot exceed the batch quantity.',
            ]);
        }

        $mortalityData = [
            'batch_id' => $healthRecord->batch_id,
            'station_id' => $healthRecord->batch->station_id,
            'user_id' => $healthRecord->user_id,
            'mortality_date' => $healthRecord->recorded_date,
            'count' => $deadCount,
            'suspected_cause' => $healthRecord->observation,
            'notes' => trim(collect([$healthRecord->remarks, $healthRecord->remedy])->filter()->implode("\n")),
        ];

        if ($mortalityRecord) {
            $mortalityRecord->update($mortalityData);
        } else {
            $mortalityRecord = MortalityRecord::create($mortalityData);
            $healthRecord->update(['mortality_record_id' => $mortalityRecord->id]);
        }
    }
    
    private function recordHistory(HealthRecord $healthRecord): void
    {
        HealthRecordHistory::create([
            'health_record_id' => $healthRecord->id,
            'user_id' => auth()->id(),
            'affected_count' => $healthRecord->affected_count,
            'dead_count' => $healthRecord->dead_count,
            'recovered_count' => $healthRecord->recovered_count,
            'status' => $healthRecord->status,
        ]);
    }
}