<?php

namespace App\Observers;

use App\Models\Batch;
use App\Models\MortalityRecord;
use App\Models\Sale;

class BatchObserver
{
    /**
     * Handle the MortalityRecord "created" event.
     */
    public function created(MortalityRecord|Sale $model): void
    {
        $batch = $model->batch;
        if ($model instanceof MortalityRecord) {
            $batch->current_quantity -= $model->count;
        } elseif ($model instanceof Sale) {
            $batch->current_quantity -= $model->heads_sold;
        }
        $batch->save();
    }
}