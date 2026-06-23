<div>
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl text-sm mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex-1 flex items-center gap-2">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pengeluaran..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if($outlets->isNotEmpty())
            <select wire:model.live="filter_outlet_id" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Outlet</option>
                @foreach($outlets as $outlet)
                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
            @endif
        </div>
        <button wire:click="create" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium whitespace-nowrap">
            + Catat Pengeluaran
        </button>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($expenses as $e)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $e->expense_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $e->description }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($e->category)
                            <span class="badge badge-amber">{{ $e->category }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-right text-red-600">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button wire:click="edit({{ $e->id }})" class="inline-flex items-center gap-1 px-2 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded hover:bg-emerald-200 text-xs font-medium transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state">
                        <p class="empty-state-text">Belum ada pengeluaran</p>
                        <p class="empty-state-sub">Klik "Catat Pengeluaran" untuk mencatat pengeluaran baru</p>
                    </td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Total</td>
                        <td class="px-4 py-3 text-sm text-right text-red-600">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

    @if($showForm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">{{ $editId ? 'Edit Pengeluaran' : 'Catat Pengeluaran' }}</h3>
                <button type="button" wire:click="closeForm" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi *</label>
                    <input type="text" wire:model="description" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    @error('description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah *</label>
                        <input type="number" wire:model="amount" step="0.01" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal *</label>
                        <input type="date" wire:model="expense_date" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @error('expense_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                    <select wire:model="category" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeForm" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                        {{ $editId ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
