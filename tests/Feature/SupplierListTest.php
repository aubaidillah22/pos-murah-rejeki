<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Supplier\SupplierList;

class SupplierListTest extends TestCase
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
    public function can_view_supplier_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/suppliers');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_supplier()
    {
        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('create')
            ->set('name', 'PT Maju Jaya')
            ->set('contact_person', 'Andi')
            ->set('phone', '021-123456')
            ->set('email', 'andi@majujaya.com')
            ->set('address', 'Jl. Industri No. 10')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Maju Jaya',
            'contact_person' => 'Andi',
        ]);
    }

    /** @test */
    public function can_read_supplier()
    {
        $supplier = Supplier::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('edit', $supplier->id)
            ->assertSet('name', $supplier->name)
            ->assertSet('phone', $supplier->phone);
    }

    /** @test */
    public function can_update_supplier()
    {
        $supplier = Supplier::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('edit', $supplier->id)
            ->set('name', 'PT Maju Jaya Updated')
            ->call('save');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'PT Maju Jaya Updated',
        ]);
    }

    /** @test */
    public function can_delete_supplier_without_purchase_orders()
    {
        $supplier = Supplier::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('confirmDelete', $supplier->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /** @test */
    public function cannot_delete_supplier_with_purchase_orders()
    {
        $supplier = Supplier::factory()->create();
        $outlet = Outlet::first();

        $supplier->purchaseOrders()->create([
            'invoice_number' => 'PO-TEST-001',
            'outlet_id' => $outlet->id,
            'user_id' => $this->admin->id,
            'total_amount' => 50000,
            'status' => 'ordered',
        ]);

        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('confirmDelete', $supplier->id)
            ->call('delete');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function can_search_suppliers()
    {
        Supplier::factory()->create(['name' => 'PT Maju Jaya']);
        Supplier::factory()->create(['name' => 'CV Sukses Abadi']);

        Livewire::actingAs($this->admin)
            ->test(SupplierList::class)
            ->set('search', 'Maju')
            ->assertSee('PT Maju Jaya')
            ->assertDontSee('CV Sukses Abadi');
    }

    /** @test */
    public function guest_cannot_access_supplier_list()
    {
        $response = $this->get('/suppliers');
        $response->assertRedirect('/login');
    }
}
