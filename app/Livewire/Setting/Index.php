<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';
    public $default_tax;
    public $store_name;
    public $store_phone;
    public $store_address;
    public $receipt_footer;
    public $store_logo;
    public $temp_logo;
    public $remove_logo = false;

    // Receipt customization
    public $receipt_width = '80mm';
    public $receipt_show_logo = true;
    public $receipt_show_address = true;
    public $receipt_show_phone = true;
    public $receipt_show_tax = true;
    public $receipt_show_discount = true;
    public $receipt_show_payment_method = true;
    public $receipt_show_change = true;
    public $receipt_show_sku = false;
    public $receipt_header = '';

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->store_name = Setting::getValue('store_name', config('app.name'));
        $this->default_tax = Setting::getValue('default_tax', '11');
        $this->store_phone = Setting::getValue('store_phone', '');
        $this->store_address = Setting::getValue('store_address', '');
        $this->receipt_footer = Setting::getValue('receipt_footer', 'Terima kasih telah berbelanja di toko kami.');
        $this->store_logo = Setting::getValue('store_logo', '');
        $this->receipt_width = Setting::getValue('receipt_width', '80mm');
        $this->receipt_show_logo = Setting::getValue('receipt_show_logo', '1') === '1';
        $this->receipt_show_address = Setting::getValue('receipt_show_address', '1') === '1';
        $this->receipt_show_phone = Setting::getValue('receipt_show_phone', '1') === '1';
        $this->receipt_show_tax = Setting::getValue('receipt_show_tax', '1') === '1';
        $this->receipt_show_discount = Setting::getValue('receipt_show_discount', '1') === '1';
        $this->receipt_show_payment_method = Setting::getValue('receipt_show_payment_method', '1') === '1';
        $this->receipt_show_change = Setting::getValue('receipt_show_change', '1') === '1';
        $this->receipt_show_sku = Setting::getValue('receipt_show_sku', '0') === '1';
        $this->receipt_header = Setting::getValue('receipt_header', '');
    }

    protected function rules()
    {
        return [
            'store_name' => 'required|string|max:100',
            'default_tax' => 'required|numeric|min:0|max:100',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_width' => 'required|in:58mm,80mm',
            'temp_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function save()
    {
        $this->validate();

        Setting::setValue('store_name', $this->store_name);
        Setting::setValue('default_tax', $this->default_tax);
        Setting::setValue('store_phone', $this->store_phone);
        Setting::setValue('store_address', $this->store_address);
        Setting::setValue('receipt_footer', $this->receipt_footer);
        Setting::setValue('receipt_width', $this->receipt_width);
        Setting::setValue('receipt_show_logo', $this->receipt_show_logo ? '1' : '0');
        Setting::setValue('receipt_show_address', $this->receipt_show_address ? '1' : '0');
        Setting::setValue('receipt_show_phone', $this->receipt_show_phone ? '1' : '0');
        Setting::setValue('receipt_show_tax', $this->receipt_show_tax ? '1' : '0');
        Setting::setValue('receipt_show_discount', $this->receipt_show_discount ? '1' : '0');
        Setting::setValue('receipt_show_payment_method', $this->receipt_show_payment_method ? '1' : '0');
        Setting::setValue('receipt_show_change', $this->receipt_show_change ? '1' : '0');
        Setting::setValue('receipt_show_sku', $this->receipt_show_sku ? '1' : '0');
        Setting::setValue('receipt_header', $this->receipt_header);

        if ($this->temp_logo) {
            $filename = 'logo.' . $this->temp_logo->getClientOriginalExtension();
            $this->temp_logo->storeAs('settings', $filename, 'public');
            Setting::setValue('store_logo', $filename);
            $this->store_logo = $filename;
            $this->temp_logo = null;
        }

        if ($this->remove_logo && !$this->temp_logo) {
            if ($this->store_logo) {
                Storage::disk('public')->delete('settings/' . $this->store_logo);
            }
            Setting::setValue('store_logo', '');
            $this->store_logo = '';
            $this->remove_logo = false;
        }

        activity()->log('Pengaturan toko diperbarui');

        $this->dispatch('settings-saved');
        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.setting.settings')
            ->layout('layouts.app', ['title' => 'Pengaturan']);
    }
}
