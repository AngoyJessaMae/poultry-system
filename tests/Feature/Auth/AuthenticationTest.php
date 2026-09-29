<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'policy_revision' => null,
            'policy_viewed_at' => null,
            'policy_accepted_at' => null,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('account-policy', ['return_to' => 'dashboard'], absolute: false));
        $this->get(route('account-policy', ['return_to' => 'dashboard']))->assertOk();
        $this->post(route('account-policy.accept'), ['policy_acknowledged' => '1'])
            ->assertRedirect(route('worker.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user->fresh());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_must_acknowledge_the_policy_before_authenticating(): void
    {
        $user = User::factory()->create([
            'policy_revision' => null,
            'policy_viewed_at' => null,
            'policy_accepted_at' => null,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('account-policy', ['return_to' => 'dashboard'], absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_with_an_outdated_policy_are_sent_to_reaccept_it(): void
    {
        $user = User::factory()->create([
            'policy_revision' => 'old-revision',
            'policy_accepted_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('account-policy', ['return_to' => 'dashboard'], absolute: false));

        $this->get(route('account-policy', ['return_to' => 'dashboard']))->assertOk();
        $this->post(route('account-policy.accept'), ['policy_acknowledged' => '1'])
            ->assertRedirect(route('worker.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame(config('policy.revision'), $user->fresh()->policy_revision);
    }

    public function test_authenticated_users_with_an_outdated_policy_cannot_enter_the_dashboard(): void
    {
        $user = User::factory()->create([
            'policy_revision' => 'old-revision',
            'policy_accepted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('account-policy', absolute: false).'?return_to=dashboard');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
