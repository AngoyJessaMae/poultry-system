<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Batch;
use App\Models\Station;
use App\Models\User;

class FeedingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'station_id',
        'user_id',
        'feed_type',
        'quantity',
        'quantity_kg',
        'unit',
        'method',
        'feeding_time',
        'scheduled_at',
        'notes',
    ];

    protected $casts = [
        'feeding_time' => 'datetime',
        'quantity' => 'decimal:2',
        'quantity_kg' => 'decimal:3',
        'scheduled_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getQuantityInKgAttribute(): float
    {
        return (float) ($this->quantity_kg ?? ((float) $this->quantity / 1000));
    }
}
