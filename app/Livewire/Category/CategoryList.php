<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Livewire\Component;

class CategoryList extends Component
{
    public $name;
    public $description;
    public $editId = null;
    public $showForm = false;

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
            Category::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Kategori berhasil diupdate!');
        } else {
            Category::create($data);
            session()->flash('success', 'Kategori berhasil ditambahkan!');
        }

        $this->reset(['name', 'description', 'editId', 'showForm']);
    }

    public function toggleActive($id)
    {
        $cat = Category::findOrFail($id);
        $cat->update(['is_active' => !$cat->is_active]);
    }

    public function render()
    {
        return view('livewire.category.category-list', [
            'categories' => Category::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Kategori']);
    }
}
