<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Outlet;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Expense\ExpenseList;

class ExpenseListTest extends TestCase
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
    public function can_view_expense_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/expenses');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_expense()
    {
        Livewire::actingAs($this->admin)
            ->test(ExpenseList::class)
            ->call('create')
            ->set('description', 'Listrik Bulanan')
            ->set('amount', 500000)
            ->set('expense_date', now()->format('Y-m-d'))
            ->set('category', 'Listrik')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Listrik Bulanan',
            'amount' => 500000,
            'outlet_id' => $this->outlet->id,
        ]);

        $this->assertDatabaseHas('cash_flows', [
            'transaction_type' => 'expense',
            'amount' => 500000,
        ]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(ExpenseList::class)
            ->call('create')
            ->set('description', '')
            ->set('amount', '')
            ->set('expense_date', '')
            ->call('save')
            ->assertHasErrors(['description' => 'required'])
            ->assertHasErrors(['amount' => 'required']);
    }

    /** @test */
    public function validates_numeric_amount()
    {
        Livewire::actingAs($this->admin)
            ->test(ExpenseList::class)
            ->call('create')
            ->set('description', 'Test Expense')
            ->set('amount', 'not-a-number')
            ->set('expense_date', now()->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['amount' => 'numeric']);
    }

    /** @test */
    public function guest_cannot_access_expense_list()
    {
        $response = $this->get('/expenses');
        $response->assertRedirect('/login');
    }
}
