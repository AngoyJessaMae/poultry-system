<?php

namespace App\Observers;

use App\Models\Batch;
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

    /**
     * Handle the MortalityRecord "updated" event.
     */
    public function updated(MortalityRecord $mortalityRecord): void
    {
        $originalBatchId = $mortalityRecord->getOriginal('batch_id');
        $originalCount = (int) $mortalityRecord->getOriginal('count');

        if ((int) $mortalityRecord->batch_id !== (int) $originalBatchId) {
            $oldBatch = Batch::find($originalBatchId);
            if ($oldBatch) {
                $oldBatch->increment('current_quantity', $originalCount);
            }

            $mortalityRecord->batch->decrement('current_quantity', $mortalityRecord->count);
            return;
        }

        $countDifference = (int) $mortalityRecord->count - $originalCount;
        if ($countDifference !== 0) {
            $mortalityRecord->batch->decrement('current_quantity', $countDifference);
        }
    }

    /**
     * Handle the MortalityRecord "deleted" event.
     */
    public function deleted(MortalityRecord $mortalityRecord): void
    {
        $mortalityRecord->batch->increment('current_quantity', $mortalityRecord->count);
    }
}