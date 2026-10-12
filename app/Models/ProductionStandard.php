<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStandard extends Model
{
    protected $fillable = [
        'age_days',
        'mortality_percent',
        'live_weight_grams',
        'feed_grams',
        'fcr',
    ];

    protected $casts = [
        'mortality_percent' => 'decimal:4',
        'live_weight_grams' => 'decimal:2',
        'feed_grams' => 'decimal:2',
        'fcr' => 'decimal:6',
    ];
}