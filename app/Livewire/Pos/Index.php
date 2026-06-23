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

    // Member pricing
    public $isMemberCustomer = false;
    public $memberDiscountPercent = 0;
    public $totalSavings = 0;

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

    public function quickAddBySearch()
    {
        if (empty($this->search)) return;

        $outletId = auth()->user()->outlet_id;
        $product = Product::where(function ($q) {
                $q->where('sku', $this->search)
                  ->orWhere('barcode', $this->search);
            })
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->first();

        if ($product) {
            $this->addToCart($product->id);
        }
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock <= 0) {
            session()->flash('error', 'Stok produk habis!');
            return;
        }

        $isMember = $this->customer_id && $this->isMemberCustomer;
        $effectivePrice = $this->getEffectivePrice($product, $isMember);

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
            $normalPrice = (float) $product->selling_price;
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'selling_price' => $normalPrice,
                'member_price' => $isMember ? $effectivePrice : null,
                'price_used' => $effectivePrice,
                'is_member_pricing' => $isMember,
                'quantity' => 1,
                'stock' => $product->stock,
                'unit' => $product->unit?->name ?? 'Unit',
                'sub_total' => $effectivePrice,
            ];
        }

        $this->search = '';
        $this->products = [];

        if ($this->isMemberCustomer) {
            $this->calculateSavings();
        }

        $this->dispatch('focus-search');
    }

    private function getEffectivePrice(Product $product, bool $isMember): float
    {
        if (!$isMember) {
            return (float) $product->selling_price;
        }

        // Use specific member_price if set
        if ($product->member_price !== null && $product->member_price > 0) {
            return (float) $product->member_price;
        }

        // Otherwise apply default member discount
        $discountPercent = (float) $this->memberDiscountPercent;
        $price = (float) $product->selling_price;
        return $price - ($price * $discountPercent / 100);
    }

    public function recalculateCartPrices()
    {
        $isMember = $this->customer_id && $this->isMemberCustomer;
        
        foreach ($this->cart as $index => $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $effectivePrice = $this->getEffectivePrice($product, $isMember);
            $this->cart[$index]['selling_price'] = (float) $product->selling_price;
            $this->cart[$index]['member_price'] = $isMember ? $effectivePrice : null;
            $this->cart[$index]['price_used'] = $effectivePrice;
            $this->cart[$index]['is_member_pricing'] = $isMember;
            $this->cart[$index]['sub_total'] = $effectivePrice * $item['quantity'];
        }

        $this->calculateSavings();
    }

    private function calculateSavings()
    {
        $this->totalSavings = 0;
        foreach ($this->cart as $item) {
            if ($item['is_member_pricing'] ?? false) {
                $normalTotal = $item['selling_price'] * $item['quantity'];
                $memberTotal = $item['sub_total'];
                $this->totalSavings += $normalTotal - $memberTotal;
            }
        }
    }

    public function updateQuantity($index, $quantity)
    {
        $quantity = max(1, (int) $quantity);
        $maxStock = $this->cart[$index]['stock'];
        
        if ($quantity > $maxStock) {
            $quantity = $maxStock;
        }

        $priceUsed = $this->cart[$index]['price_used'] ?? $this->cart[$index]['selling_price'];
        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['sub_total'] = $quantity * $priceUsed;
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
            $this->isMemberCustomer = $customer->is_member;
            
            if ($this->isMemberCustomer) {
                $this->memberDiscountPercent = (float) ($customer->discount_percent ?? 5);
            } else {
                $this->memberDiscountPercent = 0;
            }

            // Recalculate cart prices with member pricing
            if (count($this->cart) > 0) {
                $this->recalculateCartPrices();
            }
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
        $this->isMemberCustomer = false;
        $this->memberDiscountPercent = 0;
        $this->showCustomerModal = false;

        if (count($this->cart) > 0) {
            $this->recalculateCartPrices();
        }
    }

    public function getCartTotalProperty()
    {
        return array_sum(array_column($this->cart, 'sub_total'));
    }

    public function getCartTotalNormalProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['selling_price'] * $item['quantity'];
        }
        return $total;
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
        $this->calculateSavings();
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
                      'showPaymentModal', 'showReceiptModal', 'lastTransaction',
                      'isMemberCustomer', 'memberDiscountPercent', 'totalSavings']);
        $this->dispatch('focus-search');
    }

    public function render()
    {
        $this->paid_amount = max($this->paid_amount, 0);

        return view('livewire.pos.index', [
            'cart_total' => $this->cart_total,
            'cart_total_normal' => $this->cart_total_normal,
            'grand_total' => $this->grand_total,
            'change_amount' => $this->change_amount,
        ])->layout('layouts.app', ['title' => 'POS / Kasir']);
    }
}
