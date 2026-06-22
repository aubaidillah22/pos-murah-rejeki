<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Transaction\TransactionList;

class TransactionListTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Outlet $outlet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, OutletSeeder::class]);

        $this->outlet = Outlet::first();
        Category::factory()->create(['outlet_id' => $this->outlet->id]);
        Unit::factory()->create();

        $this->user = User::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);
        $this->user->assignRole('Admin');
    }

    private function createTransaction(array $overrides = []): Transaction
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        $transaction = Transaction::create(array_merge([
            'invoice_number' => 'INV-TEST-' . uniqid(),
            'customer_id' => $customer->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->user->id,
            'transaction_date' => now(),
            'total_amount' => 50000,
            'discount' => 0,
            'tax' => 0,
            'grand_total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ], $overrides));

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'selling_price' => 25000,
            'sub_total' => 50000,
        ]);

        return $transaction;
    }

    /** @test */
    public function can_view_transaction_list_page()
    {
        $response = $this->actingAs($this->user)->get('/transactions');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_view_transaction_detail()
    {
        $transaction = $this->createTransaction();

        Livewire::actingAs($this->user)
            ->test(TransactionList::class)
            ->call('viewDetail', $transaction->id)
            ->assertSet('showDetail', fn($detail) => $detail !== null && $detail->id === $transaction->id);
    }

    /** @test */
    public function can_filter_transactions_by_date()
    {
        $this->createTransaction(['transaction_date' => now()->subDays(5)]);
        $this->createTransaction(['transaction_date' => now()]);

        Livewire::actingAs($this->user)
            ->test(TransactionList::class)
            ->set('dateFrom', now()->format('Y-m-d'))
            ->set('dateTo', now()->format('Y-m-d'))
            ->assertSet('dateFrom', now()->format('Y-m-d'));
    }

    /** @test */
    public function can_filter_transactions_by_payment_method()
    {
        $this->createTransaction(['payment_method' => 'cash']);
        $this->createTransaction(['payment_method' => 'qris']);

        Livewire::actingAs($this->user)
            ->test(TransactionList::class)
            ->set('paymentMethod', 'qris')
            ->assertSet('paymentMethod', 'qris');
    }

    /** @test */
    public function can_search_transactions_by_invoice()
    {
        $this->createTransaction(['invoice_number' => 'INV-SEARCH-001']);
        $this->createTransaction(['invoice_number' => 'INV-OTHER-002']);

        Livewire::actingAs($this->user)
            ->test(TransactionList::class)
            ->set('search', 'SEARCH')
            ->assertSee('INV-SEARCH-001');
    }

    /** @test */
    public function guest_cannot_access_transaction_list()
    {
        $response = $this->get('/transactions');
        $response->assertRedirect('/login');
    }
}
