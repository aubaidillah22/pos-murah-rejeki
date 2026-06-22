<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Purchase Orders</h2>
        <button wire:click="create" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">+ PO Baru</button>
    </div>

    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Supplier</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($purchases as $po)
                <tr class="table-row">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $po->invoice_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $po->supplier?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-emerald-600 font-medium">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full 
                            @if($po->status === 'received') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                            @elseif($po->status === 'ordered') bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($po->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($po->status !== 'received')
                        <button wire:click="receiveOrder({{ $po->id }})" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Terima</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state"><p class="empty-state-text">Belum ada purchase order</p></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $purchases->links() }}</div>
    </div>

    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 overflow-y-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-2xl w-full mx-4 my-8">
            <h3 class="text-lg font-semibold mb-4">Buat Purchase Order</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier *</label>
                <select wire:model="supplier_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Produk</label>
                <input type="text" wire:model.live="searchProduct" placeholder="Nama produk..."
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                @if(count($searchResults) > 0)
                <div class="mt-2 border border-gray-200 dark:border-gray-600 rounded-lg max-h-40 overflow-y-auto">
                    @foreach($searchResults as $p)
                    <button wire:click="addItem({{ $p['id'] }})" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700">
                        {{ $p['name'] }} - Rp {{ number_format($p['purchase_price'], 0, ',', '.') }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="space-y-2 mb-4">
                @foreach($items as $index => $item)
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                    <span class="flex-1 text-sm">{{ $item['name'] }}</span>
                    <input type="number" value="{{ $item['quantity'] }}" wire:change="updateItemQty({{ $index }}, $event.target.value)"
                           class="w-16 text-center border border-gray-300 dark:border-gray-600 rounded text-sm py-1 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="1">
                    <span class="text-sm">x Rp {{ number_format($item['purchase_price'], 0, ',', '.') }}</span>
                    <span class="text-sm font-medium w-24 text-right">Rp {{ number_format($item['sub_total'], 0, ',', '.') }}</span>
                    <button wire:click="removeItem({{ $index }})" class="text-red-400 hover:text-red-600">&times;</button>
                </div>
                @endforeach
            </div>

            <div class="text-right text-lg font-bold border-t pt-3">
                Total: Rp {{ number_format($total_amount, 0, ',', '.') }}
            </div>

            <div class="flex gap-2 justify-end mt-4">
                <button wire:click="$set('showForm', false)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                <button wire:click="savePurchase" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Simpan PO</button>
            </div>
        </div>
    </div>
    @endif

    @if($showReceiveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-semibold mb-2">Konfirmasi Penerimaan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Stok barang akan ditambahkan secara otomatis. Lanjutkan?</p>
            <div class="flex gap-2 justify-end">
                <button wire:click="$set('showReceiveModal', false)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                <button wire:click="confirmReceive" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Ya, Terima</button>
            </div>
        </div>
    </div>
    @endif
</div>
