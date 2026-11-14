<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Batch;
use App\Models\ManagementRequest;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementRequestController extends Controller
{
    public function index()
    {
        $query = ManagementRequest::with(['requester', 'reviewer'])->latest();
        if (auth()->user()->isWorker()) {
            $query->where('requested_by', auth()->id());
        }

        return view(auth()->user()->isManager()
            ? 'manager.management-requests.index'
            : 'worker.management-requests.index', [
                'managementRequests' => $query->get(),
            ]);
    }

    public function approve(ManagementRequest $managementRequest)
    {
        abort_unless(auth()->user()->isManager(), 403);

        DB::transaction(function () use ($managementRequest) {
            $request = ManagementRequest::lockForUpdate()->findOrFail($managementRequest->id);
            abort_if($request->status !== 'pending', 409, 'This request has already been processed.');

            $payload = $request->payload;
            if ($request->request_type === 'station') {
                $this->approveStation($payload);
            } else {
                $this->approveBatch($payload, $request->requested_by);
            }

            $request->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            ActivityLogger::logActivity(auth()->user(), 'approve-management-request', "Approved {$request->request_type} request #{$request->id}");
        });

        return back()->with('success', 'Request approved and added to the operational records.');
    }

    public function reject(Request $request, ManagementRequest $managementRequest)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $data = $request->validate(['decision_notes' => ['nullable', 'string', 'max:2000']]);

        DB::transaction(function () use ($managementRequest, $data) {
            $request = ManagementRequest::lockForUpdate()->findOrFail($managementRequest->id);
            abort_if($request->status !== 'pending', 409, 'This request has already been processed.');
            $request->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'decision_notes' => $data['decision_notes'] ?? null,
            ]);

            ActivityLogger::logActivity(auth()->user(), 'reject-management-request', "Rejected {$request->request_type} request #{$request->id}");
        });

        return back()->with('success', 'Request rejected and preserved for reference.');
    }

    private function approveStation(array $payload): void
    {
        $code = strtoupper(trim($payload['station_code']));
        $exists = Station::whereRaw('LOWER(station_code) = ?', [strtolower($code)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($payload['name']))])
            ->exists();
        abort_if($exists, 422, 'A station with this identifier or name already exists.');

        Station::create(array_merge($payload, ['station_code' => $code]));
    }

    private function approveBatch(array $payload, int $requestedBy): void
    {
        $code = strtoupper(trim($payload['batch_code']));
        $exists = Batch::whereRaw('LOWER(batch_code) = ?', [strtolower($code)])->exists();
        abort_if($exists, 422, 'A batch with this identifier already exists.');

        $station = Station::lockForUpdate()->findOrFail($payload['station_id']);
        if ($payload['status'] === 'active') {
            $occupied = $station->batches()->where('status', 'active')->sum('current_quantity');
            abort_if($occupied + (int) $payload['initial_quantity'] > $station->capacity, 422, 'The selected station does not have enough capacity.');
        }

        Batch::create(array_merge($payload, [
            'batch_code' => $code,
            'created_by' => $requestedBy,
        ]));
    }
}
