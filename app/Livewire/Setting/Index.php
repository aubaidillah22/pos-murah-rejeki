<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;

class Index extends Component
{
    public $default_tax;
    public $store_phone;
    public $store_address;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->default_tax = Setting::getValue('default_tax', '11');
        $this->store_phone = Setting::getValue('store_phone', '');
        $this->store_address = Setting::getValue('store_address', '');
    }

    protected function rules()
    {
        return [
            'default_tax' => 'required|numeric|min:0|max:100',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        Setting::setValue('default_tax', $this->default_tax);
        Setting::setValue('store_phone', $this->store_phone);
        Setting::setValue('store_address', $this->store_address);

        activity()->log('Pengaturan toko diperbarui');

        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.setting.settings')
            ->layout('layouts.app', ['title' => 'Pengaturan']);
    }
}
