<?php

namespace App\Livewire\Transaction;

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
    public $showDetail = null;
    public $showVoidModal = false;
    public $voidId = null;
    public $voidReason = '';

    public function viewDetail($id)
    {
        $this->showDetail = Transaction::with(['details.product', 'customer', 'user', 'voidedBy'])
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
        $query = Transaction::with(['customer', 'user'])
            ->notVoided()
            ->when($this->search, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->when($this->paymentMethod, fn($q) => $q->where('payment_method', $this->paymentMethod))
            ->when($this->paymentStatus, fn($q) => $q->where('payment_status', $this->paymentStatus))
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy('id', 'desc');

        return view('livewire.transaction.transaction-list', [
            'transactions' => $query->paginate(15),
        ])->layout('layouts.app', ['title' => 'Transaksi']);
    }
}
