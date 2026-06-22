<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Product\ProductList;

class ProductListTest extends TestCase
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

        Category::factory()->count(3)->create(['outlet_id' => $this->outlet->id]);
        Unit::factory()->count(2)->create();
    }

    /** @test */
    public function can_view_product_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/products');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_product()
    {
        $category = Category::first();
        $unit = Unit::first();

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('create')
            ->set('name', 'Semen Tiga Roda 50kg')
            ->set('sku', 'SMT-001')
            ->set('selected_category_id', $category->id)
            ->set('selected_unit_id', $unit->id)
            ->set('purchase_price', 50000)
            ->set('selling_price', 65000)
            ->set('stock', 100)
            ->set('min_stock_alert', 10)
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('products', [
            'name' => 'Semen Tiga Roda 50kg',
            'sku' => 'SMT-001',
        ]);
    }

    /** @test */
    public function can_read_product()
    {
        $product = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('edit', $product->id)
            ->assertSet('name', $product->name)
            ->assertSet('sku', $product->sku);
    }

    /** @test */
    public function can_update_product()
    {
        $product = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('edit', $product->id)
            ->set('name', 'Semen Tiga Roda 40kg Updated')
            ->set('selling_price', 70000)
            ->call('save');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Semen Tiga Roda 40kg Updated',
            'selling_price' => 70000,
        ]);
    }

    /** @test */
    public function can_delete_product()
    {
        $product = Product::factory()->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('confirmDelete', $product->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function validates_numeric_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('create')
            ->set('name', 'Test Product')
            ->set('purchase_price', 'not-a-number')
            ->set('selling_price', 'not-a-number')
            ->call('save')
            ->assertHasErrors(['purchase_price' => 'numeric']);
    }

    /** @test */
    public function can_search_products()
    {
        Product::factory()->create([
            'name' => 'Besi Beton 10mm',
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);
        Product::factory()->create([
            'name' => 'Cat Tembok Nippon',
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->set('search', 'Besi')
            ->assertSee('Besi Beton 10mm')
            ->assertDontSee('Cat Tembok Nippon');
    }

    /** @test */
    public function can_sort_products()
    {
        Product::factory()->create([
            'name' => 'B',
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);
        Product::factory()->create([
            'name' => 'A',
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->call('sortBy', 'name')
            ->assertSeeInOrder(['A', 'B']);
    }

    /** @test */
    public function paginates_products()
    {
        Product::factory()->count(20)->create([
            'outlet_id' => $this->outlet->id,
            'category_id' => Category::first()->id,
            'unit_id' => Unit::first()->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductList::class)
            ->assertSee('Showing')
            ->assertSee('results');
    }

    /** @test */
    public function guest_cannot_access_product_list()
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }
}
