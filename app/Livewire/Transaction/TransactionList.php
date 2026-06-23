<?php

namespace App\Livewire\Transaction;

use App\Models\Customer;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Rap2hpoutre\FastExcel\FastExcel;

class TransactionList extends Component
{
    use WithPagination;

    public $search = '';
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

    public function exportExcel()
    {
        $query = Transaction::with(['customer', 'user', 'outlet'])
            ->notVoided()
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhere('transaction_date', 'like', '%' . $this->search . '%')
                      ->orWhere('total_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('discount', 'like', '%' . $this->search . '%')
                      ->orWhere('grand_total', 'like', '%' . $this->search . '%')
                      ->orWhere('paid_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('change_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%')
                      ->orWhere('payment_method', 'like', '%' . $this->search . '%')
                      ->orWhere('payment_status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy('id', 'desc')
            ->get();

        $exportData = $query->map(function ($t) {
            return [
                'Invoice' => $t->invoice_number,
                'Tanggal' => $t->transaction_date->format('d/m/Y H:i'),
                'Outlet' => $t->outlet?->name ?? '-',
                'Pelanggan' => $t->customer?->name ?? 'Umum',
                'Kasir' => $t->user?->name,
                'Total' => $t->total_amount,
                'Diskon' => $t->discount,
                'Grand Total' => $t->grand_total,
                'Dibayar' => $t->paid_amount,
                'Kembalian' => $t->change_amount,
                'Metode Bayar' => $t->payment_method === 'cash' ? 'Tunai' : ($t->payment_method === 'qris' ? 'QRIS' : ucfirst($t->payment_method)),
                'Status' => $t->payment_status === 'paid' ? 'Lunas' : 'Piutang',
                'Catatan' => $t->notes ?? '',
            ];
        });

        return (new FastExcel($exportData))->download('transaksi-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function render()
    {
        $query = Transaction::with(['customer', 'user'])
            ->notVoided()
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhere('transaction_date', 'like', '%' . $this->search . '%')
                      ->orWhere('total_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('discount', 'like', '%' . $this->search . '%')
                      ->orWhere('grand_total', 'like', '%' . $this->search . '%')
                      ->orWhere('paid_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('change_amount', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%')
                      ->orWhere('payment_method', 'like', '%' . $this->search . '%')
                      ->orWhere('payment_status', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy('id', 'desc');

        return view('livewire.transaction.transaction-list', [
            'transactions' => $query->paginate(15),
            'customers' => Customer::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Transaksi']);
    }
}
