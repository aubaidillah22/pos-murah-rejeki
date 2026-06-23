<?php

namespace App\Livewire\Outlet;

use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

class OutletList extends Component
{
    use WithPagination;

    public $showForm = false;
    public $showDeleteModal = false;
    public $editId = null;
    public $deleteId = null;
    public $search = '';

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
        $newStatus = !$outlet->is_active;
        $outlet->update(['is_active' => $newStatus]);
        $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
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
            $this->closeDeleteModal();
            session()->flash('error', 'Outlet tidak dapat dihapus karena masih memiliki data terkait (pengguna, produk, transaksi, dll). Nonaktifkan saja outlet ini.');
            return;
        }

        activity()->performedOn($outlet)->log('Outlet dihapus: ' . $outlet->name);
        $outlet->delete();
        $this->closeDeleteModal();
        session()->flash('success', 'Outlet berhasil dihapus!');
    }

    public function closeForm()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['editId', 'name', 'address', 'phone', 'email']);
        $this->is_active = true;
    }

    public function exportExcel()
    {
        $outlets = Outlet::orderBy('name')->get();

        $exportData = $outlets->map(function ($o) {
            return [
                'Nama' => $o->name,
                'Alamat' => $o->address ?? '',
                'Telepon' => $o->phone ?? '',
                'Email' => $o->email ?? '',
                'Status' => $o->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        return (new FastExcel($exportData))->download('outlet-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function render()
    {
        $outlets = Outlet::where(function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('address', 'like', "%{$this->search}%")
              ->orWhere('phone', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%");
        })->orderBy('name')->paginate(15);

        return view('livewire.outlet.outlet-list', compact('outlets'))
            ->layout('layouts.app', ['title' => 'Outlet']);
    }
}
