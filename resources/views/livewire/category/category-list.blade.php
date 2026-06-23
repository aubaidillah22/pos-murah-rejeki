<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Manajemen Kategori</h2>
        <button wire:click="create" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">+ Tambah Kategori</button>
    </div>

    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produk</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($categories as $cat)
                <tr class="table-row {{ !$cat->is_active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $cat->description ?? '-' }}</td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ $cat->products_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <button wire:click="toggleActive({{ $cat->id }})" 
                                class="text-xs px-2 py-1 rounded-full transition-colors duration-150
                                {{ $cat->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button wire:click="edit({{ $cat->id }})" class="inline-flex items-center gap-1 px-2 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded hover:bg-emerald-200 text-xs font-medium transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        <button wire:click="confirmDelete({{ $cat->id }})" class="inline-flex items-center gap-1 px-2 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded hover:bg-red-200 text-xs font-medium transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">{{ $editId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kategori *</label>
                    <input type="text" wire:model="name" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                    <textarea wire:model="description" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Hapus Kategori?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-2 justify-end">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Hapus</button>
            </div>
        </div>
    </div>
    @endif
</div>
