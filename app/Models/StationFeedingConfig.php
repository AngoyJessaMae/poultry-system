<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationFeedingConfig extends Model
{
    protected $fillable = ['station_id', 'method', 'schedule_times'];

    protected $casts = ['schedule_times' => 'array'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
