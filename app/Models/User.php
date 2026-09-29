<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'password',
        'role',
        'is_active',
        'policy_revision',
        'policy_viewed_at',
        'policy_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'policy_viewed_at' => 'datetime',
            'policy_accepted_at' => 'datetime',
        ];
    }

    public function hasAcceptedCurrentPolicy(): bool
    {
        return $this->policy_revision === config('policy.revision')
            && $this->policy_accepted_at !== null;
    }

    public function acceptCurrentPolicy(?string $viewedAt = null): void
    {
        $this->forceFill([
            'policy_revision' => config('policy.revision'),
            'policy_viewed_at' => $viewedAt ?? now(),
            'policy_accepted_at' => now(),
        ])->save();
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isWorker(): bool
    {
        return $this->role === 'worker';
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

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'generated_by');
    }
}