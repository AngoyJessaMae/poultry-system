<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPrice extends Model
{
    protected $fillable = ['feed_type', 'price_per_sack', 'sack_weight_kg', 'effective_from', 'effective_to', 'created_by'];

    protected $casts = [
        'price_per_sack' => 'decimal:2',
        'sack_weight_kg' => 'decimal:3',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
