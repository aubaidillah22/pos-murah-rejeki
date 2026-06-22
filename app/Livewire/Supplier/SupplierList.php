<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $contact_person;
    public $phone;
    public $email;
    public $address;
    public $editId = null;
    public $showForm = false;

    protected function rules()
    {
        return [
            'name' => 'required|min:2',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ];
    }

    public function create()
    {
        $this->reset(['name', 'contact_person', 'phone', 'email', 'address', 'editId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $s = Supplier::findOrFail($id);
        $this->editId = $s->id;
        $this->name = $s->name;
        $this->contact_person = $s->contact_person;
        $this->phone = $s->phone;
        $this->email = $s->email;
        $this->address = $s->address;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $data = ['name' => $this->name, 'contact_person' => $this->contact_person, 'phone' => $this->phone, 'email' => $this->email, 'address' => $this->address];

        if ($this->editId) {
            Supplier::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Supplier berhasil diupdate!');
        } else {
            Supplier::create($data);
            session()->flash('success', 'Supplier berhasil ditambahkan!');
        }
        $this->reset(['name', 'contact_person', 'phone', 'email', 'address', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.supplier.supplier-list', [
            'suppliers' => Supplier::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%')
                ->orderBy('name')
                ->paginate(15),
        ])->layout('layouts.app', ['title' => 'Supplier']);
    }
}
