<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex items-center gap-2 flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor opname atau produk..."
                   class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm w-64 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <select wire:model.live="typeFilter" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Tipe</option>
                <option value="surplus">Surplus (+)</option>
                <option value="shortage">Shortage (-)</option>
                <option value="correction">Koreksi</option>
            </select>
        </div>
        <button wire:click="create" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
            + Stok Opname Baru
        </button>
    </div>

    <!-- Info Card -->
    <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-4 text-sm text-amber-800 dark:text-amber-300">
        <strong>📋 Stok Opname</strong> — Catat penyesuaian stok fisik untuk material bangunan. 
        <span class="text-amber-600 dark:text-amber-400">Stok akan langsung diperbarui sesuai stok aktual.</span>
    </div>

    <!-- Table -->
    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No. Opname</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produk</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok Sistem</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok Aktual</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Selisih</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Petugas</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($opnames as $opname)
                <tr class="table-row cursor-pointer" wire:click="viewDetail({{ $opname->id }})">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $opname->opname_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $opname->product?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($opname->system_stock) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-medium">{{ number_format($opname->actual_stock) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold {{ $opname->difference > 0 ? 'text-emerald-600' : ($opname->difference < 0 ? 'text-red-600' : 'text-gray-500 dark:text-gray-400') }}">
                        {{ $opname->difference > 0 ? '+' : '' }}{{ number_format($opname->difference) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full 
                            @if($opname->type === 'surplus') bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300
                            @elseif($opname->type === 'shortage') bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($opname->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $opname->user?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-right text-xs text-gray-400">{{ $opname->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-state">
                    <p class="empty-state-icon">📦</p>
                    <p class="empty-state-text">Belum ada data stok opname</p>
                    <p class="empty-state-sub">Klik "Stok Opname Baru" untuk melakukan penyesuaian stok</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $opnames->links() }}</div>
    </div>

    <!-- Create Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-lg w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Stok Opname Baru</h3>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <!-- Product Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Produk *</label>
                    <input type="text" wire:model.live="searchProduct" 
                           placeholder="Ketik nama atau SKU produk..."
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @if(count($searchResults) > 0)
                    <div class="mt-1 border border-gray-200 dark:border-gray-600 rounded-lg max-h-40 overflow-y-auto shadow">
                        @foreach($searchResults as $p)
                        <button type="button" wire:click="selectProduct({{ $p['id'] }})" 
                                class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-medium">{{ $p['name'] }}</span>
                            <span class="text-gray-400 dark:text-gray-500 ml-2">SKU: {{ $p['sku'] ?? '-' }}</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-2">Stok: {{ $p['stock'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                    @if($selectedProductName)
                    <div class="mt-2 flex items-center bg-emerald-50 dark:bg-emerald-900/30 rounded-lg px-3 py-2 text-sm text-emerald-700 dark:text-emerald-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $selectedProductName }}
                        <button type="button" wire:click="$set('selectedProductName', null); $set('product_id', null); $set('currentStock', 0)" 
                                class="ml-auto text-red-400 hover:text-red-600">&times;</button>
                    </div>
                    @endif
                    @error('product_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                @if($product_id)
                <!-- Current Stock Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Stok Sistem Saat Ini:</span>
                        <span class="font-bold text-gray-800 dark:text-gray-100">{{ number_format($currentStock) }}</span>
                    </div>
                </div>

                <!-- Actual Stock Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stok Aktual (Fisik) *</label>
                    <input type="number" wire:model.live="actual_stock" 
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0" required>
                    @error('actual_stock') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Difference Preview -->
                @if($actual_stock !== null)
                <div class="rounded-lg p-3 {{ $difference > 0 ? 'bg-emerald-50 dark:bg-emerald-900/30' : ($difference < 0 ? 'bg-red-50 dark:bg-red-900/30' : 'bg-gray-50 dark:bg-gray-700') }}">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Selisih:</span>
                        <span class="font-bold {{ $difference > 0 ? 'text-emerald-600' : ($difference < 0 ? 'text-red-600' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $difference > 0 ? '+' : '' }}{{ number_format($difference) }}
                            <span class="text-xs ml-1">
                                @if($difference > 0) (Surplus) @elseif($difference < 0) (Shortage) @else (Sama) @endif
                            </span>
                        </span>
                    </div>
                </div>
                @endif

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Penyesuaian</label>
                    <select wire:model="type" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="surplus">Surplus (Stok Lebih)</option>
                        <option value="shortage">Shortage (Stok Kurang)</option>
                        <option value="correction">Koreksi</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                              placeholder="Contoh: barang rusak, hilang, kesalahan input, dll..."></textarea>
                    @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="flex gap-2 justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="$set('showForm', false)" 
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium"
                            @if(!$product_id) disabled @endif>
                        Simpan Opname
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetail)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Stok Opname</h3>
                <button wire:click="$set('showDetail', null)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">No. Opname:</span>
                    <span class="font-medium">{{ $showDetail->opname_number }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Produk:</span>
                    <span class="font-medium">{{ $showDetail->product?->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">SKU:</span>
                    <span>{{ $showDetail->product?->sku ?? '-' }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Stok Sistem:</span>
                        <span>{{ number_format($showDetail->system_stock) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Stok Aktual:</span>
                        <span class="font-medium">{{ number_format($showDetail->actual_stock) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold mt-1 pt-1 border-t border-gray-100 dark:border-gray-700">
                        <span>Selisih:</span>
                        <span class="{{ $showDetail->difference > 0 ? 'text-emerald-600' : ($showDetail->difference < 0 ? 'text-red-600' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $showDetail->difference > 0 ? '+' : '' }}{{ number_format($showDetail->difference) }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tipe:</span>
                    <span class="px-2 py-0.5 text-xs rounded-full 
                        @if($showDetail->type === 'surplus') bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300
                        @elseif($showDetail->type === 'shortage') bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300
                        @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst($showDetail->type) }}
                    </span>
                </div>
                @if($showDetail->notes)
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 block mb-1">Catatan:</span>
                    <p class="text-sm bg-gray-50 dark:bg-gray-700 rounded-lg p-2">{{ $showDetail->notes }}</p>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Petugas:</span>
                    <span>{{ $showDetail->user?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                    <span>{{ $showDetail->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <button wire:click="$set('showDetail', null)" 
                    class="w-full mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Tutup</button>
        </div>
    </div>
    @endif
</div>
