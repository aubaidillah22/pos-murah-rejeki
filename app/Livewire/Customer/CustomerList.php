<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';
    public $name;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public $phone;
    public $email;
    public $address;
    public $is_member = false;
    public $discount_percent = 0;
    public $editId = null;
    public $showForm = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    protected function rules()
    {
        return [
            'name' => 'required|min:2',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'is_member' => 'boolean',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ];
    }

    public function updatedIsMember($value)
    {
        if (!$value) {
            $this->discount_percent = 0;
        }
    }

    public function create()
    {
        $this->reset(['name', 'phone', 'email', 'address', 'editId']);
        $this->is_member = false;
        $this->discount_percent = 0;
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
        $this->discount_percent = $c->discount_percent ?? 0;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $data = ['name' => $this->name, 'phone' => $this->phone, 'email' => $this->email, 'address' => $this->address, 'is_member' => $this->is_member, 'discount_percent' => $this->discount_percent];

        if ($this->editId) {
            $c = Customer::findOrFail($this->editId);
            $c->update($data);
            activity()->performedOn($c)->log('Pelanggan diupdate: ' . $c->name);
            session()->flash('success', 'Pelanggan berhasil diupdate!');
        } else {
            $c = Customer::create($data);
            activity()->performedOn($c)->log('Pelanggan dibuat: ' . $c->name);
            session()->flash('success', 'Pelanggan berhasil ditambahkan!');
        }
        $this->reset(['name', 'phone', 'email', 'address', 'editId', 'showForm', 'discount_percent']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $c = Customer::findOrFail($this->deleteId);

        if ($c->transactions()->count() > 0) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.');
            return;
        }

        activity()->performedOn($c)->log('Pelanggan dihapus: ' . $c->name);
        $c->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Pelanggan berhasil dihapus!');
    }

    public function exportExcel()
    {
        $customers = Customer::withCount('transactions')
            ->orderBy('name')
            ->get();

        $exportData = $customers->map(function ($c) {
            return [
                'Nama' => $c->name,
                'Telepon' => $c->phone ?? '',
                'Email' => $c->email ?? '',
                'Alamat' => $c->address ?? '',
                'Member' => $c->is_member ? 'Ya' : 'Tidak',
                'Diskon Member %' => $c->is_member ? ($c->discount_percent ?? 0) . '%' : '-',
                'Jumlah Transaksi' => $c->transactions_count,
            ];
        });

        return (new FastExcel($exportData))->download('pelanggan-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.customer.customer-list', [
            'customers' => Customer::withCount('transactions')
                ->when($this->search, function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('name')
                ->paginate(15),
        ])->layout('layouts.app', ['title' => 'Pelanggan']);
    }
}
