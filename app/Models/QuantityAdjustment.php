<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuantityAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'manager_id',
        'previous_quantity',
        'corrected_quantity',
        'reason',
        'adjusted_at',
    ];

    protected $casts = [
        'previous_quantity' => 'integer',
        'corrected_quantity' => 'integer',
        'adjusted_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
