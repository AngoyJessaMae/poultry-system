<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'scheduled_time',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}