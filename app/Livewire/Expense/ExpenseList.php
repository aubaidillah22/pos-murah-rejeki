<?php

namespace App\Livewire\Expense;

use App\Models\CashFlow;
use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithPagination;

    public $description;
    public $amount;
    public $expense_date;
    public $category;
    public $editId = null;
    public $showForm = false;

    public $categories = ['Listrik', 'Air', 'Gaji', 'Transport', 'Sewa', 'ATK', 'Lainnya'];

    protected function rules()
    {
        return [
            'description' => 'required|min:3',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'nullable|string',
        ];
    }

    public function create()
    {
        $this->reset(['description', 'amount', 'category', 'editId']);
        $this->expense_date = now()->format('Y-m-d');
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        $data = [
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'category' => $this->category,
            'outlet_id' => auth()->user()->outlet_id,
        ];

        if ($this->editId) {
            $expense = Expense::findOrFail($this->editId);
            $expense->update($data);
        } else {
            $expense = Expense::create($data);
            
            CashFlow::create([
                'transaction_type' => 'expense',
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'amount' => $this->amount,
                'description' => 'Pengeluaran: ' . $this->description,
            ]);
        }

        session()->flash('success', 'Pengeluaran berhasil dicatat!');
        $this->reset(['description', 'amount', 'expense_date', 'category', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.expense.expense-list', [
            'expenses' => Expense::orderBy('expense_date', 'desc')
                ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
                ->paginate(15),
        ])->layout('layouts.app', ['title' => 'Pengeluaran']);
    }
}
