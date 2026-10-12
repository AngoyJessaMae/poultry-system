<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyProductionRecord extends Model
{
    protected $fillable = [
        'batch_id',
        'recorded_by',
        'recorded_date',
        'mortality_heads',
        'actual_live_weight_grams',
        'feed_type',
        'actual_feed_grams',
        'notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'actual_live_weight_grams' => 'decimal:2',
        'actual_feed_grams' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}