<?php

namespace App\Livewire;

use App\Models\CashFlow;
use App\Models\Product;
use App\Models\StockOpname;
use App\Services\StockService;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpnameList extends Component
{
    use WithPagination;

    public $showForm = false;
    public $showDetail = null;

    // Search & Filters
    public $search = '';
    public $typeFilter = '';

    // Form fields
    public $product_id;
    public $actual_stock;
    public $type = 'correction';
    public $notes = '';

    // Product search
    public $searchProduct = '';
    public $searchResults = [];
    public $selectedProductName = '';
    public $currentStock = 0;

    public function create()
    {
        $this->reset(['product_id', 'actual_stock', 'type', 'notes', 'searchProduct', 'selectedProductName', 'currentStock', 'searchResults']);
        $this->showForm = true;
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 1) {
            $this->searchResults = Product::with(['category', 'unit'])
                ->where('name', 'like', '%' . $this->searchProduct . '%')
                ->orWhere('sku', 'like', '%' . $this->searchProduct . '%')
                ->where('is_active', true)
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $this->product_id = $product->id;
        $this->selectedProductName = $product->name . ' (' . $product->sku . ')';
        $this->currentStock = $product->stock;
        $this->actual_stock = $product->stock;
        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function getDifferenceProperty()
    {
        if (!$this->product_id || $this->actual_stock === null) {
            return 0;
        }
        return $this->actual_stock - $this->currentStock;
    }

    public function updatedActualStock()
    {
        if (!$this->product_id) {
            return;
        }
        $this->type = match(true) {
            $this->difference > 0 => 'surplus',
            $this->difference < 0 => 'shortage',
            default => 'correction',
        };
    }

    public function save()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'actual_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($this->product_id);
        $systemStock = $product->stock;
        $difference = $this->actual_stock - $systemStock;

        $opnameNumber = 'SO-' . now()->format('Ymd') . '-' . str_pad(StockOpname::count() + 1, 4, '0', STR_PAD_LEFT);

        $type = match(true) {
            $difference > 0 => 'surplus',
            $difference < 0 => 'shortage',
            default => 'correction',
        };

        \Illuminate\Support\Facades\DB::transaction(function () use ($product, $systemStock, $difference, $opnameNumber, $type) {
            // Create opname record
            StockOpname::create([
                'opname_number' => $opnameNumber,
                'product_id' => $this->product_id,
                'user_id' => auth()->id(),
                'outlet_id' => auth()->user()->outlet_id,
                'system_stock' => $systemStock,
                'actual_stock' => $this->actual_stock,
                'difference' => $difference,
                'type' => $type,
                'notes' => $this->notes,
            ]);

            // Update product stock via StockService
            app(StockService::class)->sync(
                product: $product,
                newStock: $this->actual_stock,
                type: 'stock_opname',
                reference: $product,
                description: "Stok Opname {$opnameNumber}: {$product->name} ({$systemStock} → {$this->actual_stock}, selisih: {$difference})",
                outletId: auth()->user()->outlet_id,
            );

            // Log activity
            activity()
                ->performedOn($product)
                ->causedBy(auth()->id())
                ->withProperties([
                    'system_stock' => $systemStock,
                    'actual_stock' => $this->actual_stock,
                    'difference' => $difference,
                    'type' => $type,
                    'opname_number' => $opnameNumber,
                ])
                ->log("Stok Opname: {$product->name} ({$systemStock} → {$this->actual_stock}, selisih: {$difference})");
        });

        $this->showForm = false;
        session()->flash('success', "Stok Opname berhasil! {$product->name}: stok sistem {$systemStock} → stok aktual {$this->actual_stock} (selisih: {$difference})");
    }

    public function viewDetail($id)
    {
        $this->showDetail = StockOpname::with(['product.category', 'product.unit', 'user'])
            ->findOrFail($id);
    }

    public function render()
    {
        $query = StockOpname::with(['product', 'product.unit', 'user'])
            ->when($this->search, function ($q) {
                $q->where('opname_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy('id', 'desc');

        return view('livewire.stock-opname-list', [
            'opnames' => $query->paginate(15),
        ])->layout('layouts.app', ['title' => 'Stok Opname']);
    }
}
