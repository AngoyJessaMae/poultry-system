<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAdjustment extends Model
{
    protected $fillable = ['adjustment_type', 'amount', 'reason', 'adjustment_date', 'status', 'created_by'];

    protected $casts = ['amount' => 'decimal:2', 'adjustment_date' => 'date'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeValid($query)
    {
        return $query->where('status', 'active');
    }
}
