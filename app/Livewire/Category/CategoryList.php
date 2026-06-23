<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;
use Rap2hpoutre\FastExcel\FastExcel;

class CategoryList extends Component
{
    public $name;
    public $description;
    public $editId = null;
    public $showForm = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:categories,name,' . ($this->editId ?? ''),
            'description' => 'nullable|string',
        ];
    }

    public function create()
    {
        $this->reset(['name', 'description', 'editId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $cat = Category::findOrFail($id);
        $this->editId = $cat->id;
        $this->name = $cat->name;
        $this->description = $cat->description;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'outlet_id' => auth()->user()->outlet_id,
        ];

        if ($this->editId) {
            $cat = Category::findOrFail($this->editId);
            $cat->update($data);
            activity()->performedOn($cat)->log('Kategori diupdate: ' . $cat->name);
            session()->flash('success', 'Kategori berhasil diupdate!');
        } else {
            $cat = Category::create($data);
            activity()->performedOn($cat)->log('Kategori dibuat: ' . $cat->name);
            session()->flash('success', 'Kategori berhasil ditambahkan!');
        }

        $this->reset(['name', 'description', 'editId', 'showForm']);
    }

    public function toggleActive($id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_active' => !$cat->is_active]);
        $status = $cat->is_active ? 'diaktifkan' : 'dinonaktifkan';
        activity()->performedOn($cat)->log("Kategori {$status}: {$cat->name}");

        if (!$this->showForm && !$this->showDeleteModal) {
            session()->flash('success', "Kategori berhasil {$status}!");
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $cat = Category::findOrFail($this->deleteId);

        if ($cat->products()->count() > 0) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.');
            return;
        }

        activity()->performedOn($cat)->log('Kategori dihapus: ' . $cat->name);
        $cat->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Kategori berhasil dihapus!');
    }

    public function exportExcel()
    {
        $categories = Category::withCount('products')
            ->when(auth()->user()->outlet_id, fn($q) => $q->where('outlet_id', auth()->user()->outlet_id))
            ->orderBy('name')
            ->get();

        $exportData = $categories->map(function ($c) {
            return [
                'Nama' => $c->name,
                'Deskripsi' => $c->description ?? '',
                'Jumlah Produk' => $c->products_count,
                'Status' => $c->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        return (new FastExcel($exportData))->download('kategori-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.category.category-list', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Kategori']);
    }
}
