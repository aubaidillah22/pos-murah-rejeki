<?php

namespace App\Livewire\Purchase;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Services\StockService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseList extends Component
{
    use WithPagination;

    public $showForm = false;
    public $showReceiveModal = false;
    public $purchaseId;
    public $supplier_id;
    public $items = [];
    public $searchProduct = '';
    public $searchResults = [];

    public function create()
    {
        $this->reset(['supplier_id', 'items', 'purchaseId']);
        $this->showForm = true;
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 1) {
            $this->searchResults = Product::where('name', 'like', '%' . $this->searchProduct . '%')
                ->where('is_active', true)
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addItem($productId)
    {
        $product = Product::findOrFail($productId);
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'purchase_price' => (float) $product->purchase_price,
            'sub_total' => (float) $product->purchase_price,
        ];
        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function updateItemQty($index, $qty)
    {
        $qty = max(1, (int) $qty);
        $this->items[$index]['quantity'] = $qty;
        $this->items[$index]['sub_total'] = $qty * $this->items[$index]['purchase_price'];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalAmountProperty()
    {
        return array_sum(array_column($this->items, 'sub_total'));
    }

    public function savePurchase()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
        ]);

        $invoiceNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT);

        $po = PurchaseOrder::create([
            'invoice_number' => $invoiceNumber,
            'supplier_id' => $this->supplier_id,
            'outlet_id' => auth()->user()->outlet_id,
            'user_id' => auth()->id(),
            'total_amount' => $this->total_amount,
            'status' => 'ordered',
        ]);

        foreach ($this->items as $item) {
            PurchaseOrderDetail::create([
                'purchase_order_id' => $po->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'purchase_price' => $item['purchase_price'],
            ]);
        }

        $this->showForm = false;
        session()->flash('success', 'Purchase Order berhasil dibuat!');
    }

    public function receiveOrder($id)
    {
        $this->purchaseId = $id;
        $this->showReceiveModal = true;
    }

    public function confirmReceive()
    {
        $po = PurchaseOrder::with('details')->findOrFail($this->purchaseId);

        foreach ($po->details as $detail) {
            $product = Product::find($detail->product_id);
            if ($product) {
                app(StockService::class)->increase(
                    product: $product,
                    quantity: $detail->quantity,
                    type: 'purchase_receive',
                    reference: $po,
                    description: "Penerimaan PO {$po->invoice_number}: {$product->name} x{$detail->quantity}",
                    outletId: $po->outlet_id,
                );
            }
        }

        $po->update(['status' => 'received']);
        $this->showReceiveModal = false;
        session()->flash('success', 'Barang berhasil diterima dan stok ditambahkan!');
    }

    public function render()
    {
        return view('livewire.purchase.purchase-list', [
            'purchases' => PurchaseOrder::with(['supplier', 'user', 'details.product'])
                ->orderBy('id', 'desc')
                ->paginate(15),
            'suppliers' => Supplier::all(),
        ])->layout('layouts.app', ['title' => 'Pembelian']);
    }
}
