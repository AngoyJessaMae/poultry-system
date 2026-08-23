<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Batch;
use App\Policies\BatchPolicy;
use App\Models\FeedingLog;
use App\Policies\FeedingLogPolicy;
use App\Models\GrowthRecord;
use App\Policies\GrowthRecordPolicy;
use App\Models\HealthRecord;
use App\Policies\HealthRecordPolicy;
use App\Models\MortalityRecord;
use App\Policies\MortalityRecordPolicy;
use App\Models\Sale;
use App\Policies\SalePolicy;
use App\Models\Station;
use App\Policies\StationPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Batch::class => BatchPolicy::class,
        FeedingLog::class => FeedingLogPolicy::class,
        GrowthRecord::class => GrowthRecordPolicy::class,
        HealthRecord::class => HealthRecordPolicy::class,
        MortalityRecord::class => MortalityRecordPolicy::class,
        Sale::class => SalePolicy::class,
        Station::class => StationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}