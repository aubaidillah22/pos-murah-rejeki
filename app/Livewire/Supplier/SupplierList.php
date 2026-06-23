<?php

namespace App\Livewire\Supplier;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

class SupplierList extends Component
{
    use WithPagination;

    public $search = '';
    public $name;
    public $contact_person;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public $phone;
    public $email;
    public $address;
    public $editId = null;
    public $showForm = false;
    public $showDeleteModal = false;
    public $deleteId = null;

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
            $s = Supplier::findOrFail($this->editId);
            $s->update($data);
            activity()->performedOn($s)->log('Supplier diupdate: ' . $s->name);
            session()->flash('success', 'Supplier berhasil diupdate!');
        } else {
            $s = Supplier::create($data);
            activity()->performedOn($s)->log('Supplier dibuat: ' . $s->name);
            session()->flash('success', 'Supplier berhasil ditambahkan!');
        }
        $this->reset(['name', 'contact_person', 'phone', 'email', 'address', 'editId', 'showForm']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $s = Supplier::findOrFail($this->deleteId);

        if ($s->purchaseOrders()->count() > 0) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Supplier tidak dapat dihapus karena memiliki riwayat purchase order.');
            return;
        }

        activity()->performedOn($s)->log('Supplier dihapus: ' . $s->name);
        $s->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Supplier berhasil dihapus!');
    }

    public function exportExcel()
    {
        $suppliers = Supplier::withCount('purchaseOrders')
            ->orderBy('name')
            ->get();

        $exportData = $suppliers->map(function ($s) {
            return [
                'Nama Perusahaan' => $s->name,
                'Kontak Person' => $s->contact_person ?? '',
                'Telepon' => $s->phone ?? '',
                'Email' => $s->email ?? '',
                'Alamat' => $s->address ?? '',
                'Jumlah PO' => $s->purchase_orders_count,
            ];
        });

        return (new FastExcel($exportData))->download('supplier-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.supplier.supplier-list', [
            'suppliers' => Supplier::withCount('purchaseOrders')
                ->when($this->search, function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('name')
                ->paginate(15),
        ])->layout('layouts.app', ['title' => 'Supplier']);
    }
}
