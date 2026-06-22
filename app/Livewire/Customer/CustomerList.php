<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $phone;
    public $email;
    public $address;
    public $is_member = false;
    public $editId = null;
    public $showForm = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:2',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'is_member' => 'boolean',
        ];
    }

    public function create()
    {
        $this->reset(['name', 'phone', 'email', 'address', 'editId']);
        $this->is_member = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $c = Customer::findOrFail($id);
        $this->editId = $c->id;
        $this->name = $c->name;
        $this->phone = $c->phone;
        $this->email = $c->email;
        $this->address = $c->address;
        $this->is_member = $c->is_member;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $data = ['name' => $this->name, 'phone' => $this->phone, 'email' => $this->email, 'address' => $this->address, 'is_member' => $this->is_member];

        if ($this->editId) {
            Customer::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pelanggan berhasil diupdate!');
        } else {
            Customer::create($data);
            session()->flash('success', 'Pelanggan berhasil ditambahkan!');
        }
        $this->reset(['name', 'phone', 'email', 'address', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.customer.customer-list', [
            'customers' => Customer::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%')
                ->orderBy('name')
                ->paginate(15),
        ])->layout('layouts.app', ['title' => 'Pelanggan']);
    }
}
