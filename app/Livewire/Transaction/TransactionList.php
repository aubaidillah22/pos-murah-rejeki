<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $paymentMethod = '';
    public $paymentStatus = '';
    public $filter_outlet_id = '';
    public $showDetail = null;
    public $showVoidModal = false;
    public $voidId = null;
    public $voidReason = '';
    public $showEditModal = false;
    public $editId = null;
    public $editNotes = '';
    public $editCustomerId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPaymentMethod()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterOutletId()
    {
        $this->resetPage();
    }

    public function viewDetail($id)
    {
        $this->showDetail = Transaction::with(['details.product', 'customer', 'user', 'voidedBy', 'outlet'])
            ->findOrFail($id);
    }

    public function confirmVoid($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->isVoided()) {
            session()->flash('error', 'Transaksi ini sudah di-void sebelumnya.');
            return;
        }
        $this->voidId = $id;
        $this->voidReason = '';
        $this->showVoidModal = true;
    }

    public function closeDetail()
    {
        $this->showDetail = null;
    }

    public function editTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $this->editId = $transaction->id;
        $this->editNotes = $transaction->notes;
        $this->editCustomerId = $transaction->customer_id;
        $this->showEditModal = true;
    }

    public function updateTransaction()
    {
        $this->validate([
            'editNotes' => 'nullable|string|max:500',
            'editCustomerId' => 'nullable|exists:customers,id',
        ]);

        $transaction = Transaction::findOrFail($this->editId);
        $transaction->update([
            'notes' => $this->editNotes,
            'customer_id' => $this->editCustomerId ?: null,
        ]);

        activity()->performedOn($transaction)->log('Transaksi diupdate: ' . $transaction->invoice_number);
        session()->flash('success', 'Transaksi berhasil diupdate!');
        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editId = null;
        $this->editNotes = '';
        $this->editCustomerId = null;
    }

    public function closeVoidModal()
    {
        $this->showVoidModal = false;
        $this->voidId = null;
        $this->voidReason = '';
    }

    public function voidTransaction()
    {
        $this->validate(['voidReason' => 'required|min:3']);

        try {
            $posService = app(\App\Services\POSService::class);
            $posService->voidTransaction($this->voidId, $this->voidReason);

            $this->showVoidModal = false;
            $this->showDetail = null;
            $this->voidId = null;
            $this->voidReason = '';
            session()->flash('success', 'Transaksi berhasil di-void! Stok sudah dikembalikan.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $outlets = auth()->user()->hasRole('Admin') ? Outlet::where('is_active', true)->get() : collect();

        $query = Transaction::with(['customer', 'user'])
            ->notVoided()
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->when(auth()->user()->hasRole('Admin') && $this->filter_outlet_id, fn($q) => $q->where('outlet_id', $this->filter_outlet_id))
            ->orderBy('id', 'desc');

        return view('livewire.transaction.transaction-list', [
            'transactions' => $query->paginate(15),
            'outlets' => $outlets,
            'customers' => Customer::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Transaksi']);
    }
}
