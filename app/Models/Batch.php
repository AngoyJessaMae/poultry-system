<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'station_id',
        'created_by',
        'arrival_date',
        'initial_quantity',
        'current_quantity',
        'initial_weight_grams',
        'feeding_method',
        'status',
        'is_below_expected',
        'below_expected_reason',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'initial_weight_grams' => 'decimal:2',
        'is_below_expected' => 'boolean',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(FeedingLog::class);
    }

    public function growthRecords(): HasMany
    {
        return $this->hasMany(GrowthRecord::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function mortalityRecords(): HasMany
    {
        return $this->hasMany(MortalityRecord::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getCurrentAgeAttribute(): int
    {
        return Carbon::parse($this->arrival_date)->diffInDays(Carbon::now());
    }

    public function getExpectedStationAttribute(): ?Station
    {
        return Station::where('min_age_days', '<=', $this->current_age)
            ->where('max_age_days', '>=', $this->current_age)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}