<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Unit\UnitList;

class UnitListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, OutletSeeder::class]);

        $outlet = Outlet::first();
        $this->admin = User::factory()->create([
            'outlet_id' => $outlet->id,
            'is_active' => true,
        ]);
        $this->admin->assignRole('Admin');
    }

    /** @test */
    public function can_view_unit_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/units');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_unit()
    {
        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('create')
            ->set('name', 'KG')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('units', ['name' => 'KG']);
    }

    /** @test */
    public function can_read_unit()
    {
        $unit = Unit::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('edit', $unit->id)
            ->assertSet('name', $unit->name);
    }

    /** @test */
    public function can_update_unit()
    {
        $unit = Unit::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('edit', $unit->id)
            ->set('name', 'Kilogram')
            ->call('save');

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'name' => 'Kilogram',
        ]);
    }

    /** @test */
    public function can_delete_unit()
    {
        $unit = Unit::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('delete', $unit->id);

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function validates_unique_name()
    {
        Unit::factory()->create(['name' => 'PCS']);

        Livewire::actingAs($this->admin)
            ->test(UnitList::class)
            ->call('create')
            ->set('name', 'PCS')
            ->call('save')
            ->assertHasErrors(['name' => 'unique']);
    }

    /** @test */
    public function guest_cannot_access_unit_list()
    {
        $response = $this->get('/units');
        $response->assertRedirect('/login');
    }
}
