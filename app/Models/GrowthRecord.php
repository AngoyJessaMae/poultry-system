<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrowthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'recorded_date',
        'age_days',
        'average_weight_grams',
        'growth_stage',
        'is_below_expected',
        'notes',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    protected $casts = [
        'recorded_date' => 'datetime',
        'average_weight_grams' => 'decimal:2',
        'is_below_expected' => 'boolean',
    ];

    public function getAvgWeightAttribute()
    {
        return $this->attributes['average_weight_grams'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}