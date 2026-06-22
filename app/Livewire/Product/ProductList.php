<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $category_id = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // Form
    public $product_id;
    public $name;
    public $sku;
    public $barcode;
    public $selected_category_id;
    public $selected_unit_id;
    public $purchase_price;
    public $selling_price;
    public $stock;
    public $min_stock_alert;
    public $product_image;
    public $description;
    public $is_active = true;

    public $showForm = false;
    public $showDeleteModal = false;
    public $deleteId;
    public $showImportModal = false;
    public $importFile;

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'sku' => 'nullable|unique:products,sku,' . $this->product_id,
            'selected_category_id' => 'nullable|exists:categories,id',
            'selected_unit_id' => 'nullable|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'product_image' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->product_id = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode;
        $this->selected_category_id = $product->category_id;
        $this->selected_unit_id = $product->unit_id;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->stock = $product->stock;
        $this->min_stock_alert = $product->min_stock_alert;
        $this->description = $product->description;
        $this->is_active = $product->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'category_id' => $this->selected_category_id,
            'unit_id' => $this->selected_unit_id,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock,
            'min_stock_alert' => $this->min_stock_alert,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->product_image) {
            $filename = time() . '_' . $this->product_image->getClientOriginalName();
            $this->product_image->storeAs('products', $filename, 'public');
            $data['product_image'] = $filename;
        }

        if ($this->product_id) {
            $product = Product::findOrFail($this->product_id);
            $product->update($data);
            activity()->performedOn($product)->log('Produk diupdate: ' . $product->name);
            session()->flash('success', 'Produk berhasil diupdate!');
        } else {
            $data['outlet_id'] = auth()->user()->outlet_id;
            $product = Product::create($data);
            activity()->performedOn($product)->log('Produk dibuat: ' . $product->name);
            session()->flash('success', 'Produk berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $product = Product::findOrFail($this->deleteId);
        activity()->performedOn($product)->log('Produk dihapus: ' . $product->name);
        $product->delete();
        $this->showDeleteModal = false;
        session()->flash('success', 'Produk berhasil dihapus!');
    }

    public function exportExcel()
    {
        $products = Product::with(['category', 'unit'])
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->get();
        $exportData = $products->map(function ($p) {
            return [
                'Nama' => $p->name,
                'SKU' => $p->sku,
                'Kategori' => $p->category?->name,
                'Satuan' => $p->unit?->name,
                'Harga Beli' => $p->purchase_price,
                'Harga Jual' => $p->selling_price,
                'Stok' => $p->stock,
                'Min Stok' => $p->min_stock_alert,
            ];
        });

        $filename = 'produk-' . now()->format('Ymd-His') . '.xlsx';
        return response()->streamDownload(function () use ($exportData) {
            echo (new FastExcel($exportData))->export('php://output');
        }, $filename);
    }

    public function importExcel()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv']);

        $collection = (new FastExcel)->import($this->importFile->getRealPath());
        
        foreach ($collection as $row) {
            $category = Category::where('name', $row['Kategori'] ?? '')->first();
            $unit = Unit::where('name', $row['Satuan'] ?? '')->first();

            Product::updateOrCreate(
                ['sku' => $row['SKU'] ?? null],
                [
                    'name' => $row['Nama'],
                    'category_id' => $category?->id,
                    'unit_id' => $unit?->id,
                    'purchase_price' => $row['Harga Beli'] ?? 0,
                    'selling_price' => $row['Harga Jual'] ?? 0,
                    'stock' => $row['Stok'] ?? 0,
                    'min_stock_alert' => $row['Min Stok'] ?? 0,
                    'outlet_id' => auth()->user()->outlet_id,
                    'is_active' => true,
                ]
            );
        }

        $this->showImportModal = false;
        session()->flash('success', 'Import produk berhasil!');
    }

    public function resetForm()
    {
        $this->reset(['product_id', 'name', 'sku', 'barcode', 'selected_category_id',
                      'selected_unit_id', 'purchase_price', 'selling_price', 'stock',
                      'min_stock_alert', 'product_image', 'description']);
        $this->is_active = true;
    }

    public function render()
    {
        $query = Product::with(['category', 'unit'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.product.product-list', [
            'products' => $query->paginate(15),
            'categories' => Category::where('is_active', true)->get(),
            'units' => Unit::all(),
        ])->layout('layouts.app', ['title' => 'Produk']);
    }
}
