<?php

namespace App\Livewire\Outlet;

use App\Models\Outlet;
use Livewire\Component;

class OutletList extends Component
{
    public $showForm = false;
    public $showDeleteModal = false;
    public $editId = null;
    public $deleteId = null;

    // Form fields
    public $name;
    public $address;
    public $phone;
    public $email;
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|min:2',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $outlet = Outlet::findOrFail($id);
        $this->editId = $outlet->id;
        $this->name = $outlet->name;
        $this->address = $outlet->address;
        $this->phone = $outlet->phone;
        $this->email = $outlet->email;
        $this->is_active = $outlet->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->editId) {
            $outlet = Outlet::findOrFail($this->editId);
            $outlet->update($data);
            activity()->performedOn($outlet)->log('Outlet diupdate: ' . $outlet->name);
            session()->flash('success', 'Outlet berhasil diupdate!');
        } else {
            $outlet = Outlet::create($data);
            activity()->performedOn($outlet)->log('Outlet dibuat: ' . $outlet->name);
            session()->flash('success', 'Outlet berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive($id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->update(['is_active' => !$outlet->is_active]);
        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';
        activity()->performedOn($outlet)->log("Outlet {$status}: {$outlet->name}");
        session()->flash('success', "Outlet berhasil {$status}!");
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $outlet = Outlet::withCount(['users', 'products', 'transactions', 'purchaseOrders', 'expenses'])
            ->findOrFail($this->deleteId);

        if ($outlet->users_count > 0 || $outlet->products_count > 0 ||
            $outlet->transactions_count > 0 || $outlet->purchase_orders_count > 0 ||
            $outlet->expenses_count > 0) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Outlet tidak dapat dihapus karena masih memiliki data terkait (pengguna, produk, transaksi, dll). Nonaktifkan saja outlet ini.');
            return;
        }

        activity()->performedOn($outlet)->log('Outlet dihapus: ' . $outlet->name);
        $outlet->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Outlet berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->reset(['editId', 'name', 'address', 'phone', 'email']);
        $this->is_active = true;
    }

    public function render()
    {
        $outlets = Outlet::orderBy('name')->get();

        return view('livewire.outlet.outlet-list', compact('outlets'))
            ->layout('layouts.app', ['title' => 'Outlet']);
    }
}
