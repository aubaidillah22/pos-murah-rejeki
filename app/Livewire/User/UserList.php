<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Outlet;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    public $showForm = false;
    public $showDeleteModal = false;
    public $editId = null;
    public $deleteId = null;

    // Form fields
    public $name;
    public $email;
    public $password;
    public $selected_outlet_id;
    public $selected_role;
    public $is_active = true;

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,' . ($this->editId ?? ''),
            'selected_outlet_id' => 'nullable|exists:outlets,id',
            'selected_role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ];

        if (!$this->editId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        return $rules;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selected_outlet_id = $user->outlet_id;
        $this->selected_role = $user->roles->first()?->name ?? '';
        $this->is_active = $user->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'outlet_id' => $this->selected_outlet_id,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editId) {
            $user = User::findOrFail($this->editId);
            $user->update($data);
            $user->syncRoles([$this->selected_role]);
            activity()->performedOn($user)->log('Pengguna diupdate: ' . $user->name);
            session()->flash('success', 'Pengguna berhasil diupdate!');
        } else {
            $data['password'] = bcrypt($this->password);
            $user = User::create($data);
            $user->assignRole($this->selected_role);
            activity()->performedOn($user)->log('Pengguna dibuat: ' . $user->name);
            session()->flash('success', 'Pengguna berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        activity()->performedOn($user)->log("Pengguna {$status}: {$user->name}");
        session()->flash('success', "Pengguna berhasil {$status}!");
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->showDeleteModal = false;
            session()->flash('error', 'Anda tidak dapat menghapus akun sendiri!');
            return;
        }

        activity()->performedOn($user)->log('Pengguna dihapus: ' . $user->name);
        $user->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Pengguna berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->reset(['editId', 'name', 'email', 'password', 'selected_outlet_id', 'selected_role']);
        $this->is_active = true;
    }

    public function render()
    {
        $users = User::with(['outlet', 'roles'])->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->get();
        $roles = Role::all();

        return view('livewire.user.user-list', compact('users', 'outlets', 'roles'))
            ->layout('layouts.app', ['title' => 'Pengguna']);
    }
}
