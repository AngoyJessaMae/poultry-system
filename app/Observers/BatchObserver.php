<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\BatchMovement;
use Illuminate\Support\Facades\Auth;

class BatchObserver
{
    public function created(Batch $batch)
    {
        // This space is intentionally left blank.
        // The logic for feeding schedules has been moved to a manual, action-first model.
    }

    public function updated(Batch $batch)
    {
        // Handle station change logging
        if ($batch->isDirty('station_id')) {
            BatchMovement::create([
                'batch_id' => $batch->id,
                'from_station_id' => $batch->getOriginal('station_id'),
                'to_station_id' => $batch->station_id,
                'user_id' => Auth::id(),
                'moved_at' => now(),
            ]);
        }
    }
}