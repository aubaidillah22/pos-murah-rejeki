<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
            <button wire:click="$set('activeTab', 'sales')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'sales' ? 'bg-white dark:bg-gray-800 shadow text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">Penjualan</button>
            <button wire:click="$set('activeTab', 'profit')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'profit' ? 'bg-white dark:bg-gray-800 shadow text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">Laba/Rugi</button>
            <button wire:click="$set('activeTab', 'stock')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'stock' ? 'bg-white dark:bg-gray-800 shadow text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">Stok</button>
            <button wire:click="$set('activeTab', 'cashflow')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'cashflow' ? 'bg-white dark:bg-gray-800 shadow text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">Arus Kas</button>
            <button wire:click="$set('activeTab', 'expenses')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'expenses' ? 'bg-white dark:bg-gray-800 shadow text-emerald-700 dark:text-emerald-400 font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">Pengeluaran</button>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="dateFrom" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <span class="text-gray-400">-</span>
            <input type="date" wire:model.live="dateTo" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <button wire:click="exportPdf('{{ $activeTab }}')" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">📄 PDF</button>
            <button wire:click="exportExcel('{{ $activeTab }}')" class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">⬇ Excel</button>
        </div>
    </div>

    <!-- Sales Report -->
    @if($activeTab === 'sales')
    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pelanggan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kasir</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Diskon</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Grand Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($salesReport as $s)
                <tr class="table-row">
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $s->invoice_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $s->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $s->customer?->name ?? 'Umum' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $s->user?->name }}</td>
                    <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-500">Rp {{ number_format($s->discount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right text-emerald-600">Rp {{ number_format($s->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada data penjualan</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Total</td>
                    <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($salesReport->sum('total_amount'), 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-500">Rp {{ number_format($salesReport->sum('discount'), 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-emerald-600">Rp {{ number_format($salesReport->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @elseif($activeTab === 'profit')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Penjualan</p>
            <h3 class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($profitLoss['total_sales'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">HPP (Harga Pokok Penjualan)</p>
            <h3 class="text-xl font-bold text-orange-600 mt-1">Rp {{ number_format($profitLoss['hpp'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Laba Kotor</p>
            <h3 class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($profitLoss['gross_profit'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
            <h3 class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($profitLoss['total_expenses'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="card p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Laba Bersih</p>
            <h3 class="text-xl font-bold {{ ($profitLoss['net_profit'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                Rp {{ number_format($profitLoss['net_profit'] ?? 0, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    @elseif($activeTab === 'stock')
    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Satuan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stok</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Min Stok</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($stockReport as $p)
                <tr class="table-row">
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $p->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $p->category?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $p->unit?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($p->stock) }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ $p->min_stock_alert }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $p->isStockLow() ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' : 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' }}">
                            {{ $p->isStockLow() ? 'Menipis' : 'Aman' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data stok</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Total Produk: {{ $stockReport->count() }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($stockReport->sum('stock')) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @elseif($activeTab === 'cashflow')
    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($cashFlow as $cf)
                <tr class="table-row">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $cf->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $cf->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $cf->transaction_type === 'income' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' }}">
                            {{ $cf->transaction_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-right {{ $cf->transaction_type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($cf->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada data arus kas</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        <span class="text-green-600">Pemasukan: Rp {{ number_format($cashFlow->where('transaction_type', 'income')->sum('amount'), 0, ',', '.') }}</span>
                        <span class="mx-2">|</span>
                        <span class="text-red-600">Pengeluaran: Rp {{ number_format($cashFlow->where('transaction_type', 'expense')->sum('amount'), 0, ',', '.') }}</span>
                        <span class="mx-2">|</span>
                        <span class="{{ $cashFlow->where('transaction_type', 'income')->sum('amount') - $cashFlow->where('transaction_type', 'expense')->sum('amount') >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            Saldo: Rp {{ number_format($cashFlow->where('transaction_type', 'income')->sum('amount') - $cashFlow->where('transaction_type', 'expense')->sum('amount'), 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-right"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @elseif($activeTab === 'expenses')
    <div class="mb-3">
        <select wire:model.live="category" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">Semua Kategori</option>
            <option value="Listrik">Listrik</option>
            <option value="Air">Air</option>
            <option value="Gaji">Gaji</option>
            <option value="Transport">Transport</option>
            <option value="Sewa">Sewa</option>
            <option value="ATK">ATK</option>
            <option value="Lainnya">Lainnya</option>
        </select>
    </div>
    <div class="table-wrap">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="table-header">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($expenses as $e)
                <tr class="table-row">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $e->expense_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $e->description }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $e->category ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-right text-red-600">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada data pengeluaran</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Total</td>
                    <td class="px-4 py-3 text-sm text-right text-red-600">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
