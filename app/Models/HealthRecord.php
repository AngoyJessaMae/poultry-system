<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    
    public function history(): HasMany
    {
        return $this->hasMany(HealthRecordHistory::class)->latest();
    }
    
    public function getRecoveredCountAttribute(): int
    {
        return max(0, (int) $this->affected_count - (int) $this->dead_count);
    }

    protected $casts = [
        'recorded_date' => 'datetime',
    ];
}