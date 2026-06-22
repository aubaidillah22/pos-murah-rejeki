<?php

namespace App\Livewire\Unit;

use App\Models\Unit;
use Livewire\Component;

class UnitList extends Component
{
    public $name;
    public $editId = null;
    public $showForm = false;
    public $showDeleteModal = false;
    public $deleteId;

    protected function rules()
    {
        return ['name' => 'required|min:1|unique:units,name,' . ($this->editId ?? '')];
    }

    public function create()
    {
        $this->reset(['name', 'editId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->editId = $unit->id;
        $this->name = $unit->name;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();
        if ($this->editId) {
            Unit::findOrFail($this->editId)->update(['name' => $this->name]);
            session()->flash('success', 'Satuan berhasil diupdate!');
        } else {
            Unit::create(['name' => $this->name]);
            session()->flash('success', 'Satuan berhasil ditambahkan!');
        }
        $this->reset(['name', 'editId', 'showForm']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Unit::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Satuan berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.unit.unit-list', [
            'units' => Unit::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Satuan']);
    }
}
