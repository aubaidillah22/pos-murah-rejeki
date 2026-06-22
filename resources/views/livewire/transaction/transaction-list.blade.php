<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex-1 flex flex-wrap items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari invoice..."
                   class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm w-40 dark:bg-gray-700 dark:text-gray-100">
            <input type="date" wire:model.live="dateFrom" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100">
            <input type="date" wire:model.live="dateTo" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100">
            <select wire:model.live="paymentMethod" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100">
                <option value="">Semua Metode</option>
                <option value="cash">Tunai</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
                <option value="debit">Debit</option>
            </select>
            <select wire:model.live="paymentStatus" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100">
                <option value="">Semua Status</option>
                <option value="paid">Lunas</option>
                <option value="due">Piutang</option>
            </select>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pelanggan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kasir</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($transactions as $t)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" wire:click="viewDetail({{ $t->id }})">
                    <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $t->invoice_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $t->customer?->name ?? 'Umum' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $t->user?->name }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-right text-emerald-600">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $t->payment_status === 'paid' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300' }}">
                            {{ $t->payment_status === 'paid' ? 'Lunas' : 'Piutang' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-400 dark:text-gray-500">{{ ucfirst($t->payment_method) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $transactions->links() }}</div>
    </div>

    <!-- Detail Modal -->
    @if($showDetail)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-lg w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                <button wire:click="$set('showDetail', null)" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Invoice:</span>
                    <span class="font-medium">{{ $showDetail->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                    <span>{{ $showDetail->transaction_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Pelanggan:</span>
                    <span>{{ $showDetail->customer?->name ?? 'Umum' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Kasir:</span>
                    <span>{{ $showDetail->user?->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Metode Bayar:</span>
                    <span class="capitalize">{{ $showDetail->payment_method }}</span>
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 my-4 pt-4">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item</h4>
                @foreach($showDetail->details as $d)
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600 dark:text-gray-300">{{ $d->product?->name }} x{{ $d->quantity }}</span>
                    <span>Rp {{ number_format($d->sub_total, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-1">
                @if($showDetail->discount > 0)
                <div class="flex justify-between text-sm"><span class="text-gray-500 dark:text-gray-400">Diskon</span><span class="text-red-500">-Rp {{ number_format($showDetail->discount, 0, ',', '.') }}</span></div>
                @endif
                @if($showDetail->tax > 0)
                <div class="flex justify-between text-sm"><span class="text-gray-500 dark:text-gray-400">Pajak</span><span>Rp {{ number_format($showDetail->tax, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between text-base font-bold pt-1">
                    <span>Grand Total</span>
                    <span class="text-emerald-600">Rp {{ number_format($showDetail->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Dibayar</span>
                    <span>Rp {{ number_format($showDetail->paid_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($showDetail->notes)
            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-300">
                <strong>Catatan:</strong> {{ $showDetail->notes }}
            </div>
            @endif

            <div class="flex gap-2 mt-4">
                <button wire:click="$set('showDetail', null)" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Tutup</button>
                @can('delete-transactions')
                <button wire:click="confirmVoid({{ $showDetail->id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
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
                <button wire:click="$set('showVoidModal', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
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
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100"
                              placeholder="Contoh: kesalahan input, pesanan dibatalkan pelanggan, dll..." required></textarea>
                    @error('voidReason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="$set('showVoidModal', false)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                        Ya, Void Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
