<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unacknowledged_worker_is_redirected_before_dashboard_access(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'house_rules_revision' => null,
            'house_rules_acknowledged_at' => null,
        ]);

        $this->actingAs($worker)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('house-rules', ['return_to' => 'dashboard'], false));
    }

    public function test_unacknowledged_manager_is_redirected_before_dashboard_access(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'house_rules_revision' => null,
            'house_rules_acknowledged_at' => null,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.dashboard'))
            ->assertRedirect(route('house-rules', ['return_to' => 'dashboard'], false));
    }

    public function test_user_can_acknowledge_house_rules_and_access_dashboard(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'house_rules_revision' => null,
            'house_rules_acknowledged_at' => null,
        ]);

        $this->actingAs($worker)
            ->get(route('house-rules', ['return_to' => 'dashboard']))
            ->assertOk()
            ->assertSee('Poultry Farm House Rules')
            ->assertSee('Basaha ug sabta ang tanang house rules');

        $this->post(route('house-rules.acknowledge'), [
            'house_rules_acknowledged' => '1',
        ])->assertRedirect(route('worker.dashboard', absolute: false));

        $this->assertTrue($worker->refresh()->hasAcknowledgedCurrentHouseRules());
    }

    public function test_login_redirects_user_to_house_rules_before_dashboard(): void
    {
        $worker = User::factory()->create([
            'role' => 'worker',
            'house_rules_revision' => null,
            'house_rules_acknowledged_at' => null,
        ]);

        $this->post('/login', [
            'email' => $worker->email,
            'password' => 'password',
        ])->assertRedirect(route('house-rules', ['return_to' => 'dashboard'], false));
    }

    public function test_acknowledged_user_can_view_house_rules_again(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);

        $this->actingAs($worker)
            ->get(route('house-rules'))
            ->assertOk()
            ->assertSee('You have already acknowledged these house rules.');
    }
}