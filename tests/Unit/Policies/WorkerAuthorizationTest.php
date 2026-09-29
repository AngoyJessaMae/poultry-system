<?php

namespace Tests\Unit\Policies;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\MortalityRecord;
use App\Models\Sale;
use App\Models\Station;
use App\Models\User;
use App\Policies\BatchPolicy;
use App\Policies\FeedingLogPolicy;
use App\Policies\GrowthRecordPolicy;
use App\Policies\MortalityRecordPolicy;
use App\Policies\SalePolicy;
use App\Policies\StationPolicy;
use PHPUnit\Framework\TestCase;

class WorkerAuthorizationTest extends TestCase
{
    public function test_worker_can_update_and_delete_operational_records(): void
    {
        $worker = new User(['role' => 'worker']);

        $abilities = [
            [new StationPolicy(), new Station(), 'update'],
            [new StationPolicy(), new Station(), 'delete'],
            [new BatchPolicy(), new Batch(), 'update'],
            [new BatchPolicy(), new Batch(), 'delete'],
            [new FeedingLogPolicy(), new FeedingLog(), 'update'],
            [new FeedingLogPolicy(), new FeedingLog(), 'delete'],
            [new MortalityRecordPolicy(), new MortalityRecord(), 'update'],
            [new MortalityRecordPolicy(), new MortalityRecord(), 'delete'],
            [new SalePolicy(), new Sale(), 'update'],
            [new SalePolicy(), new Sale(), 'delete'],
            [new GrowthRecordPolicy(), new GrowthRecord(), 'update'],
            [new GrowthRecordPolicy(), new GrowthRecord(), 'delete'],
        ];

        foreach ($abilities as [$policy, $model, $ability]) {
            $this->assertTrue($policy->{$ability}($worker, $model));
        }
    }
}