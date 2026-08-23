<?php

namespace App\Models;

use App\Enums\FeedingMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_age_days',
        'max_age_days',
        'description',
        'feeding_method',
    ];

    protected $casts = [
        'feeding_method' => FeedingMethod::class,
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function feedingSchedules(): HasMany
    {
        return $this->hasMany(FeedingSchedule::class);
    }
}