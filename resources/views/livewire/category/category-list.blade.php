<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Manajemen Kategori</h2>
        <button wire:click="create" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">+ Tambah Kategori</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($categories as $cat)
                <tr class="hover:bg-gray-50 {{ !$cat->is_active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $cat->description ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $cat->products_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <button wire:click="toggleActive({{ $cat->id }})" 
                                class="text-xs px-2 py-1 rounded-full transition-colors
                                {{ $cat->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button wire:click="edit({{ $cat->id }})" class="text-emerald-600 hover:text-emerald-800 text-sm mr-2">Edit</button>
                        <button wire:click="confirmDelete({{ $cat->id }})" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">{{ $editId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                    <input type="text" wire:model="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hapus Kategori?</h3>
            <p class="text-sm text-gray-500 mb-4">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2 justify-end">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>
