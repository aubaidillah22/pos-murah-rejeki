<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Customer\CustomerList;

class CustomerListTest extends TestCase
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
    public function can_view_customer_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/customers');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_customer()
    {
        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('create')
            ->set('name', 'Budi Santoso')
            ->set('phone', '08123456789')
            ->set('email', 'budi@example.com')
            ->set('address', 'Jl. Merdeka No. 1')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('customers', [
            'name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);
    }

    /** @test */
    public function can_read_customer()
    {
        $customer = Customer::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('edit', $customer->id)
            ->assertSet('name', $customer->name)
            ->assertSet('phone', $customer->phone);
    }

    /** @test */
    public function can_update_customer()
    {
        $customer = Customer::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('edit', $customer->id)
            ->set('name', 'Budi Santoso Updated')
            ->call('save');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Budi Santoso Updated',
        ]);
    }

    /** @test */
    public function can_delete_customer_without_transactions()
    {
        $customer = Customer::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('confirmDelete', $customer->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    /** @test */
    public function cannot_delete_customer_with_transactions()
    {
        $customer = Customer::factory()->create();

        $customer->transactions()->create([
            'invoice_number' => 'INV-TEST-001',
            'outlet_id' => Outlet::first()->id,
            'user_id' => $this->admin->id,
            'transaction_date' => now(),
            'total_amount' => 10000,
            'grand_total' => 10000,
            'paid_amount' => 10000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('confirmDelete', $customer->id)
            ->call('delete');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function can_search_customers()
    {
        Customer::factory()->create(['name' => 'Ahmad Subarjo']);
        Customer::factory()->create(['name' => 'Budi Santoso']);

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->set('search', 'Ahmad')
            ->assertSee('Ahmad Subarjo')
            ->assertDontSee('Budi Santoso');
    }

    /** @test */
    public function can_export_excel()
    {
        Customer::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(CustomerList::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function guest_cannot_access_customer_list()
    {
        $response = $this->get('/customers');
        $response->assertRedirect('/login');
    }
}
