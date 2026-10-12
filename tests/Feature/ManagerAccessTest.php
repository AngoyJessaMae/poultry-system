<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Batch;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_users_and_reports(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.users.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('manager.reports.index'))
            ->assertOk();
    }

    public function test_manager_can_filter_all_reports_by_station_and_batch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);
        $station = Station::create([
            'name' => 'Report Filter Station',
            'min_age_days' => 1,
            'max_age_days' => 60,
        ]);
        $batch = Batch::create([
            'batch_code' => 'REPORT-FILTER-' . uniqid(),
            'station_id' => $station->id,
            'created_by' => $manager->id,
            'arrival_date' => now()->subDays(3),
            'initial_quantity' => 100,
            'initial_weight_grams' => 45,
            'feeding_method' => 'twice_daily',
            'status' => 'active',
        ]);

        $this->actingAs($manager)
            ->get(route('manager.reports.index', [
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->toDateString(),
                'station_id' => $station->id,
                'batch_id' => $batch->id,
                'report_type' => 'all',
            ]))
            ->assertOk();
    }

    public function test_manager_login_redirects_to_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->get(route('account-policy', ['return_to' => 'login']))
            ->assertOk();

        $this->post('/login', [
            'email' => $manager->email,
            'password' => 'password',
            'policy_acknowledged' => '1',
        ])->assertRedirect(route('manager.dashboard', absolute: false));
    }

    public function test_manager_operational_routes_are_not_registered(): void
    {
        $removedRoutes = [
            'manager.stations.index',
            'manager.batches.index',
            'manager.feeding-logs.index',
            'manager.growth-records.index',
            'manager.health-records.index',
            'manager.mortality-records.index',
            'manager.sales.index',
        ];

        foreach ($removedRoutes as $routeName) {
            $this->assertFalse(\Illuminate\Support\Facades\Route::has($routeName));
        }
    }
}