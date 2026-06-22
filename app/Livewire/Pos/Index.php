<?php

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Models\Product;
use App\Services\POSService;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    public $search = '';
    public $products = [];
    public $cart = [];
    public $customer_id = null;
    public $customer_name = '';
    public $customer_phone = '';
    public $customer_search = '';
    public $customers = [];
    public $showCustomerModal = false;
    public $discount = 0;
    public $tax = 0;
    public $payment_method = 'cash';
    public $paid_amount = 0;
    public $payment_status = 'paid';
    public $notes = '';
    public $showPaymentModal = false;
    public $showReceiptModal = false;
    public $lastTransaction = null;

    protected function rules()
    {
        return [
            'cart' => 'required|array|min:1',
            'payment_method' => 'required|in:cash,qris,transfer,debit',
            'paid_amount' => 'required|numeric|min:0',
        ];
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 1) {
            $outletId = auth()->user()->outlet_id;
            $this->products = Product::with(['category', 'unit'])
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                ->limit(20)
                ->get()
                ->toArray();
        } else {
            $this->products = [];
        }
    }

    public function updatedCustomerSearch()
    {
        if (strlen($this->customer_search) >= 1) {
            $this->customers = Customer::where('name', 'like', '%' . $this->customer_search . '%')
                ->orWhere('phone', 'like', '%' . $this->customer_search . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->customers = [];
        }
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock <= 0) {
            session()->flash('error', 'Stok produk habis!');
            return;
        }

        $existingKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] == $productId) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $this->cart[$existingKey]['quantity']++;
            $this->cart[$existingKey]['sub_total'] = $this->cart[$existingKey]['quantity'] * $this->cart[$existingKey]['selling_price'];
            
            if ($this->cart[$existingKey]['quantity'] > $product->stock) {
                $this->cart[$existingKey]['quantity'] = $product->stock;
                $this->cart[$existingKey]['sub_total'] = $this->cart[$existingKey]['quantity'] * $this->cart[$existingKey]['selling_price'];
            }
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'selling_price' => (float) $product->selling_price,
                'quantity' => 1,
                'stock' => $product->stock,
                'unit' => $product->unit?->name ?? 'Unit',
                'sub_total' => (float) $product->selling_price,
            ];
        }

        $this->search = '';
        $this->products = [];
    }

    public function updateQuantity($index, $quantity)
    {
        $quantity = max(1, (int) $quantity);
        $maxStock = $this->cart[$index]['stock'];
        
        if ($quantity > $maxStock) {
            $quantity = $maxStock;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['sub_total'] = $quantity * $this->cart[$index]['selling_price'];
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->customer_id = $customer->id;
            $this->customer_name = $customer->name;
        }
        $this->showCustomerModal = false;
    }

    public function createCustomer()
    {
        $this->validate([
            'customer_name' => 'required|min:3',
            'customer_phone' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $this->customer_name,
            'phone' => $this->customer_phone,
        ]);

        $this->customer_id = $customer->id;
        $this->customer_name = $customer->name;
        $this->showCustomerModal = false;
    }

    public function getCartTotalProperty()
    {
        return array_sum(array_column($this->cart, 'sub_total'));
    }

    public function getGrandTotalProperty()
    {
        $total = $this->cart_total;
        $total -= $this->discount;
        $total += $this->tax;
        return max(0, $total);
    }

    public function getChangeAmountProperty()
    {
        return max(0, $this->paid_amount - $this->grand_total);
    }

    public function openPaymentModal()
    {
        if (count($this->cart) === 0) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }
        $this->paid_amount = $this->grand_total;
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        $this->validate();

        if ($this->paid_amount < $this->grand_total && $this->payment_status === 'paid') {
            $this->payment_status = 'due';
        }

        try {
            $posService = app(POSService::class);
            
            $items = [];
            foreach ($this->cart as $item) {
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'discount' => 0,
                ];
            }

            $this->lastTransaction = $posService->processTransaction([
                'items' => $items,
                'customer_id' => $this->customer_id,
                'outlet_id' => auth()->user()->outlet_id,
                'user_id' => auth()->id(),
                'discount' => $this->discount,
                'tax' => $this->tax,
                'paid_amount' => $this->paid_amount,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'notes' => $this->notes,
            ]);

            $this->showPaymentModal = false;
            $this->showReceiptModal = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function newTransaction()
    {
        $this->reset(['cart', 'customer_id', 'customer_name', 'discount', 'tax', 
                      'paid_amount', 'payment_method', 'payment_status', 'notes',
                      'showPaymentModal', 'showReceiptModal', 'lastTransaction']);
    }

    public function render()
    {
        $this->paid_amount = max($this->paid_amount, 0);
        
        return view('livewire.pos.index', [
            'cart_total' => $this->cart_total,
            'grand_total' => $this->grand_total,
            'change_amount' => $this->change_amount,
        ])->layout('layouts.app', ['title' => 'POS / Kasir']);
    }
}
