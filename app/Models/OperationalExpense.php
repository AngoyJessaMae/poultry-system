<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalExpense extends Model
{
    protected $fillable = ['description', 'category', 'amount', 'quantity', 'unit', 'expense_date', 'allocation_type', 'station_id', 'batch_id', 'notes', 'status', 'cancelled_at', 'cancelled_by', 'cancellation_reason', 'created_by'];

    protected $casts = ['amount' => 'decimal:2', 'quantity' => 'decimal:3', 'expense_date' => 'date', 'cancelled_at' => 'datetime'];

    public function station(): BelongsTo { return $this->belongsTo(Station::class); }
    public function batch(): BelongsTo { return $this->belongsTo(Batch::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }

    public function scopeValid($query)
    {
        return $query->where('status', 'active');
    }
}
