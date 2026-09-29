<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'contact_number' => '09859945478',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('account-policy', ['return_to' => 'register'], absolute: false));
        $this->get(route('account-policy', ['return_to' => 'register']))->assertOk();
        $response = $this->post(route('account-policy.accept'), ['policy_acknowledged' => '1']);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'policy_revision' => config('policy.revision'),
        ]);
    }

    public function test_registration_rejects_contact_numbers_that_are_not_exactly_11_digits(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'contact_number' => '09859ABC',
            'password' => 'password',
            'password_confirmation' => 'password',
            'policy_acknowledged' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('contact_number');
    }

    public function test_registration_requires_policy_acknowledgment(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'policy@example.com',
            'contact_number' => '09859945478',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('account-policy', ['return_to' => 'register'], absolute: false));
        $response->assertSessionHasErrors('policy_acknowledged');
    }
}
