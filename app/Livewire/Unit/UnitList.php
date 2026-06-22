<?php

namespace App\Livewire\Unit;

use App\Models\Unit;
use Livewire\Component;

class UnitList extends Component
{
    public $name;
    public $editId = null;
    public $showForm = false;

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

    public function delete($id)
    {
        Unit::findOrFail($id)->delete();
        session()->flash('success', 'Satuan berhasil dihapus!');
    }

    public function render()
    {
        return view('livewire.unit.unit-list', [
            'units' => Unit::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Satuan']);
    }
}
