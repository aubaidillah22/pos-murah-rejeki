<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div class="flex-1 flex flex-wrap items-center gap-2">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice, pelanggan, kasir..."
                       class="w-56 pl-9 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if($search)
                <button wire:click="$set('search', '')" class="absolute right-2 top-2 p-0.5 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>
            <div class="flex items-center gap-1">
                <label class="text-xs text-gray-400 dark:text-gray-500 mr-1">Dari</label>
                <input type="date" wire:model.live="dateFrom" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex items-center gap-1">
                <label class="text-xs text-gray-400 dark:text-gray-500 mr-1">Sampai</label>
                <input type="date" wire:model.live="dateTo" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <select wire:model.live="paymentMethod" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Metode</option>
                <option value="cash">Tunai</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
                <option value="debit">Debit</option>
            </select>
            <select wire:model.live="paymentStatus" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Status</option>
                <option value="paid">Lunas</option>
                <option value="due">Piutang</option>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kasir</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $t)
                    <tr class="table-row cursor-pointer" wire:click="viewDetail({{ $t->id }})">
                        <td class="cell-primary">{{ $t->invoice_number }}</td>
                        <td class="cell-secondary">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
                        <td class="cell-secondary">{{ $t->customer?->name ?? 'Umum' }}</td>
                        <td class="cell-secondary">{{ $t->user?->name }}</td>
                        <td class="cell-amount">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $t->payment_status === 'paid' ? 'badge-green' : 'badge-amber' }}">
                                {{ $t->payment_status === 'paid' ? 'Lunas' : 'Piutang' }}
                            </span>
                        </td>
                        <td class="cell-secondary text-right">{{ $t->payment_method === 'cash' ? 'Tunai' : ucfirst($t->payment_method) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><p class="empty-state-text">Belum ada transaksi</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $transactions->links() }}</div>
    </div>

    <!-- Detail Modal -->
    @if($showDetail)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Detail Transaksi</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $showDetail->invoice_number }}</p>
                    </div>
                </div>
                <button wire:click="closeDetail" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Info -->
            <div class="px-6 py-4 space-y-3">
                <div class="grid grid-cols-2 gap-x-6 gap-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $showDetail->transaction_date->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Kasir</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $showDetail->user?->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Pelanggan</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $showDetail->customer?->name ?? 'Umum' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Metode Bayar</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200 capitalize">{{ $showDetail->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span>
                            <span class="badge {{ $showDetail->payment_status === 'paid' ? 'badge-green' : 'badge-amber' }}">
                                {{ $showDetail->payment_status === 'paid' ? 'Lunas' : 'Piutang' }}
                            </span>
                        </span>
                    </div>
                    @if($showDetail->isVoided())
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Void</span>
                        <span class="badge badge-red">Ya</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="px-6 pb-2">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Item
                </h4>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($showDetail->details as $d)
                    <div class="flex justify-between items-center px-3 py-2.5 text-sm">
                        <div class="flex-1 min-w-0">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $d->product?->name ?? 'Produk Dihapus' }}</span>
                            <span class="text-gray-400 dark:text-gray-500 ml-1">x{{ $d->quantity }}</span>
                        </div>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 ml-3">Rp {{ number_format($d->sub_total, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals -->
            <div class="px-6 pb-2">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 space-y-1.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($showDetail->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($showDetail->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Diskon</span>
                        <span class="text-red-500 font-medium">-Rp {{ number_format($showDetail->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($showDetail->tax > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Pajak</span>
                        <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($showDetail->tax, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold pt-1.5 border-t border-gray-200 dark:border-gray-600">
                        <span class="text-gray-800 dark:text-gray-100">Grand Total</span>
                        <span class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($showDetail->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-0.5">
                        <span class="text-gray-500 dark:text-gray-400">Dibayar</span>
                        <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($showDetail->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($showDetail->change_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Kembalian</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">Rp {{ number_format($showDetail->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($showDetail->notes)
            <div class="px-6 pb-2">
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Catatan: </span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $showDetail->notes }}</span>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button wire:click="closeDetail" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-medium text-center">
                    Tutup
                </button>
                <a href="{{ route('transactions.print', $showDetail->id) }}" target="_blank"
                   class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium text-center whitespace-nowrap">
                    🖨️ Cetak Ulang
                </a>
                @can('delete-transactions')
                <button wire:click="confirmVoid({{ $showDetail->id }})" 
                        class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium whitespace-nowrap">
                    🔄 Void
                </button>
                @endcan
            </div>
        </div>
    </div>
    @endif

    <!-- Void Confirmation Modal -->
    @if($showVoidModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Void Transaksi</h3>
                <button wire:click="closeVoidModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-3 mb-4 text-sm text-red-700 dark:text-red-300">
                <strong>⚠️ Perhatian!</strong> Transaksi yang di-void akan:
                <ul class="list-disc ml-4 mt-1">
                    <li>Mengembalikan stok produk</li>
                    <li>Mencatat arus kas pengeluaran (refund)</li>
                    <li>Tidak bisa dibatalkan</li>
                </ul>
            </div>

            <form wire:submit="voidTransaction" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan Void *</label>
                    <textarea wire:model="voidReason" rows="3" 
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Contoh: kesalahan input, pesanan dibatalkan pelanggan, dll..." required></textarea>
                    @error('voidReason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="closeVoidModal" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                        Ya, Void Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
