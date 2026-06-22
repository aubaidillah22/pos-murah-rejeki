<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
            <button wire:click="$set('activeTab', 'sales')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'sales' ? 'bg-white shadow text-emerald-700 font-medium' : 'text-gray-500 hover:text-gray-700' }}">Penjualan</button>
            <button wire:click="$set('activeTab', 'profit')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'profit' ? 'bg-white shadow text-emerald-700 font-medium' : 'text-gray-500 hover:text-gray-700' }}">Laba/Rugi</button>
            <button wire:click="$set('activeTab', 'stock')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'stock' ? 'bg-white shadow text-emerald-700 font-medium' : 'text-gray-500 hover:text-gray-700' }}">Stok</button>
            <button wire:click="$set('activeTab', 'cashflow')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'cashflow' ? 'bg-white shadow text-emerald-700 font-medium' : 'text-gray-500 hover:text-gray-700' }}">Arus Kas</button>
            <button wire:click="$set('activeTab', 'expenses')" class="px-3 py-2 text-sm rounded-md {{ $activeTab === 'expenses' ? 'bg-white shadow text-emerald-700 font-medium' : 'text-gray-500 hover:text-gray-700' }}">Pengeluaran</button>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="dateFrom" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <span class="text-gray-400">-</span>
            <input type="date" wire:model.live="dateTo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button wire:click="exportPdf('{{ $activeTab }}')" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">📄 PDF</button>
            <button wire:click="exportExcel('{{ $activeTab }}')" class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">⬇ Excel</button>
        </div>
    </div>

    <!-- Sales Report -->
    @if($activeTab === 'sales')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Diskon</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Grand Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($salesReport as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $s->invoice_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $s->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $s->customer?->name ?? 'Umum' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $s->user?->name }}</td>
                    <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-500">Rp {{ number_format($s->discount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right text-emerald-600">Rp {{ number_format($s->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada data penjualan</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-sm text-gray-700">Total</td>
                    <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($salesReport->sum('total_amount'), 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-500">Rp {{ number_format($salesReport->sum('discount'), 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm text-right text-emerald-600">Rp {{ number_format($salesReport->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @elseif($activeTab === 'profit')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Penjualan</p>
            <h3 class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($profitLoss['total_sales'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">HPP (Harga Pokok Penjualan)</p>
            <h3 class="text-xl font-bold text-orange-600 mt-1">Rp {{ number_format($profitLoss['hpp'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Laba Kotor</p>
            <h3 class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($profitLoss['gross_profit'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Total Pengeluaran</p>
            <h3 class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($profitLoss['total_expenses'] ?? 0, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Laba Bersih</p>
            <h3 class="text-xl font-bold {{ ($profitLoss['net_profit'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                Rp {{ number_format($profitLoss['net_profit'] ?? 0, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    @elseif($activeTab === 'stock')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min Stok</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($stockReport as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $p->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $p->category?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $p->unit?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($p->stock) }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ $p->min_stock_alert }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $p->isStockLow() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $p->isStockLow() ? 'Menipis' : 'Aman' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data stok</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @elseif($activeTab === 'cashflow')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cashFlow as $cf)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $cf->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $cf->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $cf->transaction_type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
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
        </table>
    </div>

    @elseif($activeTab === 'expenses')
    <livewire:expense.expense-list />
    @endif
</div>
