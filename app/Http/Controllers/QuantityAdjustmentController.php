<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreQuantityAdjustmentRequest;
use App\Models\Batch;
use App\Models\QuantityAdjustment;
use Illuminate\Support\Facades\DB;

class QuantityAdjustmentController extends Controller
{
    public function create(Batch $batch)
    {
        abort_unless(auth()->user()->isManager(), 403);

        return view('manager.quantity-adjustments.create', compact('batch'));
    }

    public function store(StoreQuantityAdjustmentRequest $request, Batch $batch)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $batch, $data) {
            $lockedBatch = Batch::lockForUpdate()->findOrFail($batch->id);

            if ((int) $data['previous_quantity'] !== (int) $lockedBatch->current_quantity) {
                abort(409, 'The batch quantity changed while this correction was being prepared. Refresh and try again.');
            }

            $adjustment = QuantityAdjustment::create([
                'batch_id' => $lockedBatch->id,
                'manager_id' => $request->user()->id,
                'previous_quantity' => $lockedBatch->current_quantity,
                'corrected_quantity' => $data['corrected_quantity'],
                'reason' => $data['reason'],
                'adjusted_at' => now(),
            ]);

            $lockedBatch->forceFill(['current_quantity' => $adjustment->corrected_quantity])->save();

            ActivityLogger::logActivity(
                $request->user(),
                'quantity-adjustment',
                "Adjusted batch {$lockedBatch->batch_code} from {$adjustment->previous_quantity} to {$adjustment->corrected_quantity}: {$adjustment->reason}"
            );
        });

        return redirect()->route('manager.quantity-adjustments.create', $batch)
            ->with('success', 'Quantity correction recorded successfully.');
    }
}
