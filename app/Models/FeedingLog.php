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
        'feeding_schedule_id',
        'feeding_time_slot',
        'feed_type',
        'quantity_kg',
        'fed_at',
        'notes',
    ];

    protected $casts = [
        'fed_at' => 'datetime',
        'quantity_kg' => 'decimal:2',
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
}