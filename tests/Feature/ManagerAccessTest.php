<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_manager_login_redirects_to_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);

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