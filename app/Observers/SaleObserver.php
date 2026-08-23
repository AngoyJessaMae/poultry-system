<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $batch = $sale->batch;
        $batch->current_quantity -= $sale->heads_sold;
        $batch->save();
    }
}