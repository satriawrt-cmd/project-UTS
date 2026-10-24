<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('CorrectPassword123!'),
            'role' => 'staff',
        ]);

        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_owner_can_login_and_see_dashboard(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => bcrypt('OwnerDemo2026!'),
            'role' => 'owner',
        ]);

        $response = $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'OwnerDemo2026!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($owner);

        $dashResponse = $this->actingAs($owner)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Ringkasan Dashboard');
    }

    public function test_staff_can_login_and_see_dashboard(): void
    {
        $staff = User::factory()->create([
            'email' => 'staff@example.com',
            'password' => bcrypt('StaffDemo2026!'),
            'role' => 'staff',
        ]);

        $response = $this->actingAs($staff)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('staff');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
