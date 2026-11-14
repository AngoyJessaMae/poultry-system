<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\GrowthRecord;
use App\Models\MortalityRecord;
use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6DashboardMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_contains_station_overview_and_separate_monitoring_data(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $worker = User::factory()->create(['role' => 'worker', 'is_active' => true]);
        $station = Station::create(['station_code' => 'S1', 'name' => 'Chick Station', 'capacity' => 500, 'min_age_days' => 1, 'max_age_days' => 10]);
        $batch = Batch::create([
            'batch_code' => 'PHASE6-BATCH',
            'station_id' => $station->id,
            'created_by' => $manager->id,
            'arrival_date' => '2026-11-01',
            'initial_quantity' => 100,
            'current_quantity' => 98,
            'initial_weight_grams' => 45,
            'feeding_method' => 'unlimited',
            'status' => 'active',
        ]);
        FeedingLog::create(['batch_id' => $batch->id, 'station_id' => $station->id, 'user_id' => $worker->id, 'feed_type' => 'Starter', 'quantity' => 12000, 'quantity_kg' => 12, 'unit' => 'kg', 'method' => 'Free Choice', 'feeding_time' => '2026-11-05 07:00:00']);
        GrowthRecord::create(['batch_id' => $batch->id, 'user_id' => $worker->id, 'recorded_date' => '2026-11-05', 'age_days' => 5, 'average_weight_grams' => 450, 'growth_stage' => 'Chick']);
        MortalityRecord::create(['batch_id' => $batch->id, 'station_id' => $station->id, 'user_id' => $worker->id, 'mortality_date' => '2026-11-05', 'count' => 2, 'suspected_cause' => 'Test']);

        $this->actingAs($manager)
            ->get(route('manager.dashboard', ['year' => 2026, 'month' => 11]))
            ->assertOk()
            ->assertSee('Chick Station')
            ->assertSee('Feeding monitoring')
            ->assertSee('Growth monitoring')
            ->assertSee('Health monitoring')
            ->assertSee('Production monitoring')
            ->assertSee('Sales monitoring')
            ->assertSee('Financial monitoring')
            ->assertSee('12.000 kg recorded');
    }

    public function test_worker_dashboard_exposes_operational_actions_without_manager_controls(): void
    {
        $worker = User::factory()->create(['role' => 'worker', 'is_active' => true]);
        $station = Station::create(['station_code' => 'S2', 'name' => 'Growing Station', 'capacity' => 500, 'min_age_days' => 11, 'max_age_days' => 25]);
        Batch::create(['batch_code' => 'PHASE6-WORKER', 'station_id' => $station->id, 'created_by' => $worker->id, 'arrival_date' => now()->subDays(3), 'initial_quantity' => 50, 'current_quantity' => 50, 'initial_weight_grams' => 45, 'feeding_method' => 'four_times_daily', 'status' => 'active']);

        $this->actingAs($worker)
            ->get(route('worker.dashboard'))
            ->assertOk()
            ->assertSee('Daily operations')
            ->assertSee('Growing Station')
            ->assertSee('Log Growth')
            ->assertDontSee('Financial Management')
            ->assertDontSee('Sales Management');
    }

    public function test_worker_cannot_access_manager_dashboard_or_monitoring_filters(): void
    {
        $worker = User::factory()->create(['role' => 'worker', 'is_active' => true]);

        $this->actingAs($worker)
            ->get(route('manager.dashboard', ['year' => 2026]))
            ->assertForbidden();
    }
}
