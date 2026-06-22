<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\OutletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Setting\Index as SettingIndex;
use Illuminate\Support\Facades\Storage;

class SettingListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed([RoleSeeder::class, OutletSeeder::class, \Database\Seeders\UnitSeeder::class]);

        $outlet = Outlet::first();
        $this->admin = User::factory()->create([
            'outlet_id' => $outlet->id,
            'is_active' => true,
        ]);
        $this->admin->assignRole('Admin');
    }

    /** @test */
    public function can_view_settings_page()
    {
        $response = $this->actingAs($this->admin)->get('/settings');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_update_general_settings()
    {
        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('store_name', 'Toko Bangunan Baru')
            ->set('default_tax', '12')
            ->set('store_phone', '021-888888')
            ->set('store_address', 'Jl. Baru No. 1')
            ->call('save');

        $this->assertEquals('Toko Bangunan Baru', Setting::getValue('store_name'));
        $this->assertEquals('12', Setting::getValue('default_tax'));
        $this->assertEquals('021-888888', Setting::getValue('store_phone'));
        $this->assertEquals('Jl. Baru No. 1', Setting::getValue('store_address'));
    }

    /** @test */
    public function can_update_receipt_footer()
    {
        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('receipt_footer', 'Terima kasih sudah belanja!')
            ->call('save');

        $this->assertEquals('Terima kasih sudah belanja!', Setting::getValue('receipt_footer'));
    }

    /** @test */
    public function can_upload_logo()
    {
        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('temp_logo', $file)
            ->call('save');

        $logoFilename = Setting::getValue('store_logo');
        $this->assertNotEmpty($logoFilename);
        $this->assertStringContainsString('.png', $logoFilename);
    }

    /** @test */
    public function validates_store_name_required()
    {
        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('store_name', '')
            ->call('save')
            ->assertHasErrors(['store_name' => 'required']);
    }

    /** @test */
    public function validates_default_tax_range()
    {
        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('default_tax', 150)
            ->call('save')
            ->assertHasErrors(['default_tax' => 'max']);
    }

    /** @test */
    public function validates_logo_file_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->set('temp_logo', $file)
            ->call('save')
            ->assertHasErrors(['temp_logo']);
    }

    /** @test */
    public function store_name_appears_in_sidebar()
    {
        Setting::setValue('store_name', 'Toko Saya');

        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Toko Saya');
    }

    /** @test */
    public function store_name_defaults_to_config()
    {
        Setting::where('key', 'store_name')->delete();

        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee(config('app.name'));
    }

    /** @test */
    public function can_switch_tabs()
    {
        Livewire::actingAs($this->admin)
            ->test(SettingIndex::class)
            ->assertSet('activeTab', 'general')
            ->set('activeTab', 'logo')
            ->assertSet('activeTab', 'logo')
            ->set('activeTab', 'receipt')
            ->assertSet('activeTab', 'receipt');
    }

    /** @test */
    public function guest_cannot_access_settings()
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }
}
