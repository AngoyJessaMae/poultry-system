<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public const CREATED_AT = 'generated_at';
    public const UPDATED_AT = null;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'generated_by',
        'type',
        'period_start',
        'period_end',
        'generated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'generated_at' => 'datetime',
    ];

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}