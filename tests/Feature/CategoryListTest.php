<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Category\CategoryList;

class CategoryListTest extends TestCase
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
    public function can_view_category_list_page()
    {
        $response = $this->actingAs($this->admin)->get('/categories');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_category()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('create')
            ->set('name', 'Material Bangunan')
            ->set('description', 'Kategori untuk material bangunan')
            ->call('save')
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('categories', [
            'name' => 'Material Bangunan',
            'outlet_id' => $this->outlet->id,
        ]);
    }

    /** @test */
    public function can_read_category()
    {
        $category = Category::factory()->create([
            'outlet_id' => $this->outlet->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('edit', $category->id)
            ->assertSet('name', $category->name);
    }

    /** @test */
    public function can_update_category()
    {
        $category = Category::factory()->create([
            'outlet_id' => $this->outlet->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('edit', $category->id)
            ->set('name', 'Material Bangunan Updated')
            ->call('save');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Material Bangunan Updated',
        ]);
    }

    /** @test */
    public function can_delete_category_without_products()
    {
        $category = Category::factory()->create([
            'outlet_id' => $this->outlet->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('confirmDelete', $category->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function cannot_delete_category_with_products()
    {
        $category = Category::factory()->create([
            'outlet_id' => $this->outlet->id,
        ]);

        $category->products()->create([
            'name' => 'Test Product',
            'outlet_id' => $this->outlet->id,
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'min_stock_alert' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('confirmDelete', $category->id)
            ->call('delete');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function validates_required_fields()
    {
        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('create')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function can_toggle_active()
    {
        $category = Category::factory()->create([
            'outlet_id' => $this->outlet->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('toggleActive', $category->id);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function can_search_categories()
    {
        Category::factory()->create(['name' => 'Material Bangunan', 'outlet_id' => $this->outlet->id]);
        Category::factory()->create(['name' => 'Alat Tulis', 'outlet_id' => $this->outlet->id]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->set('search', 'Material')
            ->assertSee('Material Bangunan')
            ->assertDontSee('Alat Tulis');
    }

    /** @test */
    public function can_export_excel()
    {
        Category::factory()->count(3)->create([
            'outlet_id' => $this->outlet->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(CategoryList::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function guest_cannot_access_category_list()
    {
        $response = $this->get('/categories');
        $response->assertRedirect('/login');
    }
}
