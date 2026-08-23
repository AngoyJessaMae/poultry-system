<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MortalityRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'station_id',
        'user_id',
        'mortality_date',
        'count',
        'suspected_cause',
        'notes',
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

    protected $casts = [
        'mortality_date' => 'datetime',
    ];
}