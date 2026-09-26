<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'affected_count',
        'dead_count',
        'status',
        'recorded_date',
        'observation',
        'medication_name',
        'dosage_amount',
        'dosage_unit',
        'notes',
        'remarks',
        'remedy',
        'mortality_record_id',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mortalityRecord(): BelongsTo
    {
        return $this->belongsTo(MortalityRecord::class);
    }

    protected $casts = [
        'recorded_date' => 'datetime',
    ];
}