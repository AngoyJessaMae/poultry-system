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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}