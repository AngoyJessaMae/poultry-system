<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtherIncome extends Model
{
    protected $fillable = ['description', 'category', 'amount', 'income_date', 'notes', 'status', 'cancelled_at', 'cancelled_by', 'cancellation_reason', 'created_by'];

    protected $casts = ['amount' => 'decimal:2', 'income_date' => 'date', 'cancelled_at' => 'datetime'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); }

    public function scopeValid($query)
    {
        return $query->where('status', 'active');
    }
}
