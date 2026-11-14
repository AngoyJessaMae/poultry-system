<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Station;
use App\Models\User;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'station_id',
        'user_id',
        'transaction_number',
        'sale_date',
        'heads_sold',
        'total_weight_kg',
        'price_per_kg',
        'total_amount',
        'buyer_name',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'sale_date' => 'datetime',
        'total_weight_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'completed');
    }
}
