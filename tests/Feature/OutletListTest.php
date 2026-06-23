<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Outlet\OutletList;

class OutletListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, OutletSeeder::class]);

        $this->admin = User::factory()->create([
            'outlet_id' => 1,
            'is_active' => true,
        ]);
        $this->admin->assignRole('Admin');
    }

    /** @test */
    public function can_view_outlet_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/outlets');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_outlet()
    {
        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('create')
            ->set('name', 'Cabang Kedua')
            ->set('address', 'Jl. Raya No. 2')
            ->set('phone', '021-999999')
            ->set('email', 'cabang2@example.com')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('outlets', [
            'name' => 'Cabang Kedua',
        ]);
    }

    /** @test */
    public function can_read_outlet()
    {
        $outlet = Outlet::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('edit', $outlet->id)
            ->assertSet('name', $outlet->name);
    }

    /** @test */
    public function can_update_outlet()
    {
        $outlet = Outlet::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('edit', $outlet->id)
            ->set('name', 'Cabang Updated')
            ->call('save');

        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'name' => 'Cabang Updated',
        ]);
    }

    /** @test */
    public function cannot_delete_outlet_with_relations()
    {
        $outlet = Outlet::factory()->create();

        User::factory()->create([
            'outlet_id' => $outlet->id,
            'is_active' => true,
        ])->assignRole('Kasir');

        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('confirmDelete', $outlet->id)
            ->call('delete');

        $this->assertDatabaseHas('outlets', ['id' => $outlet->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function can_export_excel()
    {
        Outlet::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(OutletList::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function guest_cannot_access_outlet_list()
    {
        $response = $this->get('/outlets');
        $response->assertRedirect('/login');
    }
}
