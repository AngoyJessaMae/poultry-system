<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Station extends Model
{
    use HasFactory;

    public const ALLOWED_CODES = ['S1', 'S2', 'S3'];

    protected $fillable = [
        'station_code',
        'name',
        'capacity',
        'min_age_days',
        'max_age_days',
        'description',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function feedingConfig(): HasOne
    {
        return $this->hasOne(StationFeedingConfig::class);
    }

    public function getOccupiedCapacityAttribute(): int
    {
        $batches = $this->relationLoaded('batches')
            ? $this->batches
            : $this->batches()->get();

        return (int) $batches
            ->where('status', 'active')
            ->sum('current_quantity');
    }

    public function getAvailableCapacityAttribute(): int
    {
        return max(0, (int) $this->capacity - $this->occupied_capacity);
    }
}
