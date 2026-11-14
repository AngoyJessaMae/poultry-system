<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagementRequest extends Model
{
    protected $fillable = [
        'request_type',
        'request_key',
        'requested_by',
        'status',
        'payload',
        'reviewed_by',
        'reviewed_at',
        'decision_notes',
    ];

    protected $casts = [
        'payload' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
