<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\User\UserList;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Outlet $outlet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, OutletSeeder::class]);

        $this->outlet = Outlet::first();
        $this->admin = User::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);
        $this->admin->assignRole('Admin');
    }

    /** @test */
    public function can_view_user_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_user()
    {
        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('create')
            ->set('name', 'Kasir Baru')
            ->set('email', 'kasirbaru@example.com')
            ->set('password', 'secret123')
            ->set('selected_role', 'Kasir')
            ->set('selected_outlet_id', $this->outlet->id)
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('users', [
            'name' => 'Kasir Baru',
            'email' => 'kasirbaru@example.com',
        ]);
    }

    /** @test */
    public function can_read_user()
    {
        $user = User::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('edit', $user->id)
            ->assertSet('name', $user->name)
            ->assertSet('email', $user->email);
    }

    /** @test */
    public function can_update_user()
    {
        $user = User::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('edit', $user->id)
            ->set('name', 'Kasir Updated')
            ->call('save');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Kasir Updated',
        ]);
    }

    /** @test */
    public function can_toggle_user_active_status()
    {
        $user = User::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);
        $user->assignRole('Kasir');

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('toggleActive', $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function cannot_delete_self()
    {
        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('confirmDelete', $this->admin->id)
            ->call('delete');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required'])
            ->assertHasErrors(['email' => 'required'])
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function validates_unique_email()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ])->assignRole('Kasir');

        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->call('create')
            ->set('name', 'Test')
            ->set('email', 'existing@example.com')
            ->set('password', 'secret123')
            ->set('selected_role', 'Kasir')
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function guest_cannot_access_user_list()
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }
}
