<?php

namespace App\Observers;

use App\Models\MortalityRecord;

class MortalityRecordObserver
{
    /**
     * Handle the MortalityRecord "created" event.
     */
    public function created(MortalityRecord $mortalityRecord): void
    {
        $batch = $mortalityRecord->batch;
        $batch->current_quantity -= $mortalityRecord->count;
        $batch->save();
    }
}