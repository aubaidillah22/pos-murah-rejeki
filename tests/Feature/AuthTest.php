<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, OutletSeeder::class]);
    }

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate()
    {
        $user = User::factory()->create([
            'outlet_id' => 1,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'outlet_id' => 1,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create([
            'outlet_id' => 1,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_requires_email_and_password()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_authenticated_user_redirected_from_login()
    {
        $user = User::factory()->create([
            'outlet_id' => 1,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect();
    }

    public function test_dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
