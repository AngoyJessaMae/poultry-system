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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            $batch->current_quantity = $batch->initial_quantity;
        });
    }

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

    public function feedingSchedules(): HasMany
    {
        return $this->hasMany(FeedingSchedule::class);
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

    public function movements(): HasMany
    {
        return $this->hasMany(BatchMovement::class)->orderBy('moved_at', 'desc');
    }

    protected $appends = [
        'name',
        'current_age',
        'expected_station',
        'growth_stage',
        'is_underweight',
        'latest_average_weight'
    ];

    public function getNameAttribute(): string
    {
        return $this->batch_code . ' (' . Carbon::parse($this->arrival_date)->format('M d, Y') . ')';
    }

    public function getCurrentAgeAttribute(): int
    {
        return Carbon::parse($this->arrival_date)->diffInDays(Carbon::now());
    }

    public function getLatestAverageWeightAttribute(): ?float
    {
        return $this->growthRecords()->latest('recorded_date')->first()->average_weight ?? null;
    }

    public function getGrowthStageAttribute(): string
    {
        $ageInDays = $this->current_age;

        if ($ageInDays >= 57) {
            return 'Market Ready';
        }

        if ($ageInDays >= 29) {
            return 'Grower';
        }

        return 'Chick';
    }

    private function getExpectedWeightInKg(int $ageInDays): float
    {
        // Growth curve definition (minimum expected weight in kg)
        $growthCurve = [
            7 => 0.18,
            14 => 0.45,
            21 => 0.90,
            26 => 1.3,
            28 => 1.5,
            32 => 2.4,
            // Add more data points as needed
        ];

        // Find the closest age in the curve without going over
        $closestAge = null;
        foreach (array_keys($growthCurve) as $curveAge) {
            if ($ageInDays >= $curveAge) {
                $closestAge = $curveAge;
            } else {
                break;
            }
        }

        return $closestAge ? $growthCurve[$closestAge] : 0.0;
    }

    public function getIsUnderweightAttribute(): bool
    {
        $age = $this->current_age;
        $currentWeight = $this->latest_average_weight;

        if ($currentWeight === null || $age < 7) {
            // Not enough data or too young to tell
            return false;
        }

        $expectedWeight = $this->getExpectedWeightInKg($age);

        return $currentWeight < $expectedWeight;
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