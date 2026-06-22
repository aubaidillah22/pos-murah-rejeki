<div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalSalesToday, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalTransactionsToday }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Laba Kotor Hari Ini</p>
                    <h3 class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($profitToday, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts }}</h3>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pelanggan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCustomers }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Chart Penjualan Bulanan -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Grafik Penjualan Bulanan</h3>
                <select wire:model.live="selectedYear" 
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div id="salesChart" style="min-height: 300px;"></div>
        </div>

        <!-- Pie Chart Metode Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran Hari Ini</h3>
            @if(count($paymentMethodData) > 0)
            <div id="paymentChart" style="min-height: 250px;"></div>
            <div class="mt-4 space-y-2">
                @foreach($paymentMethodData as $pm)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full inline-block mr-2" style="background: {{ $pm['color'] }}"></span>
                        <span class="text-gray-600">{{ $pm['label'] }}</span>
                    </div>
                    <span class="font-medium">Rp {{ number_format($pm['value'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-8">Belum ada transaksi hari ini</p>
            @endif
        </div>
    </div>

    <!-- Row 2: Top Products + Recent Transactions + Top Cashier -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Produk Terlaris -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏆 Produk Terlaris Hari Ini</h3>
            <div class="space-y-3">
                @forelse($topSellingProducts as $index => $product)
                <div class="flex items-center justify-between {{ $index > 0 ? 'pt-3 border-t border-gray-100' : '' }}">
                    <div class="flex items-center flex-1 min-w-0">
                        <span class="w-6 h-6 rounded-full {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : ($index == 1 ? 'bg-gray-100 text-gray-600' : ($index == 2 ? 'bg-amber-100 text-amber-700' : 'bg-gray-50 text-gray-400')) }} flex items-center justify-center text-xs font-bold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->category?->name }} - {{ $product->unit?->name }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600 ml-2">{{ $product->total_sold }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada penjualan hari ini</p>
                @endforelse
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">📋 Transaksi Terbaru</h3>
                <a href="{{ route('transactions') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentTransactions as $t)
                <div class="flex items-center justify-between {{ !$loop->first ? 'pt-3 border-t border-gray-100' : '' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $t->invoice_number }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $t->customer?->name ?? 'Umum' }} — {{ $t->transaction_date->format('H:i') }}
                        </p>
                    </div>
                    <div class="text-right ml-2">
                        <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</p>
                        <p class="text-xs {{ $t->payment_status === 'paid' ? 'text-green-500' : 'text-yellow-500' }}">
                            {{ $t->payment_status === 'paid' ? 'Lunas' : 'Piutang' }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>

        <!-- Top Kasir -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">👨‍💼 Kinerja Kasir Hari Ini</h3>
            <div class="space-y-3">
                @forelse($topCashierToday as $index => $cashier)
                <div class="flex items-center justify-between {{ $index > 0 ? 'pt-3 border-t border-gray-100' : '' }}">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-bold text-emerald-700 mr-3">
                            {{ substr($cashier->user?->name ?? '?', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $cashier->user?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $cashier->total_trans }} transaksi</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($cashier->total_amount, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada transaksi hari ini</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notifikasi Stok Menipis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">⚠️ Notifikasi Stok Menipis</h3>
            <a href="{{ route('products') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
        </div>
        @if($lowStockProducts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($lowStockProducts as $product)
            <div class="flex items-center justify-between bg-red-50 rounded-lg p-3 border border-red-100">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-red-600">Stok: {{ $product->stock }} {{ $product->unit?->name }} (Min: {{ $product->min_stock_alert }})</p>
                </div>
                <a href="{{ route('purchases') }}" class="text-xs px-2 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 ml-2">Pesan</a>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-4">Semua stok dalam kondisi aman ✅</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    var salesChart = null;
    var paymentChart = null;

    function initSalesChart(monthlySales) {
        var options = {
            series: [{
                name: 'Penjualan',
                data: monthlySales
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: true, tools: { download: true, selection: false, zoom: false, pan: false } },
                foreColor: '#6b7280',
                zoom: { enabled: false },
                animations: { enabled: true, dynamicAnimation: { speed: 500 } }
            },
            colors: ['#059669'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            markers: { size: 4, colors: ['#059669'], strokeColors: '#fff', strokeWidth: 2 },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            },
            yaxis: {
                labels: {
                    formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); }
                }
            },
            tooltip: {
                y: { formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); } }
            },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
        };
        return new ApexCharts(document.querySelector("#salesChart"), options);
    }

    function initPaymentChart(paymentData) {
        if (paymentData.length === 0) return null;
        var options = {
            series: paymentData.map(d => d.value),
            chart: {
                type: 'donut',
                height: 250,
                foreColor: '#6b7280',
            },
            labels: paymentData.map(d => d.label),
            colors: paymentData.map(d => d.color),
            legend: { show: false },
            dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(1) + '%'; } },
            plotOptions: {
                pie: {
                    donut: { size: '55%', labels: { show: true, total: { show: true, label: 'Total', formatter: function(w) { return 'Rp ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID'); } } } }
                }
            },
            tooltip: {
                y: { formatter: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); } }
            },
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
        };
        return new ApexCharts(document.querySelector("#paymentChart"), options);
    }

    document.addEventListener('DOMContentLoaded', function() {
        var monthlySales = @json($monthlySales);
        var paymentData = @json($paymentMethodData);

        salesChart = initSalesChart(monthlySales);
        salesChart.render();

        if (paymentData.length > 0) {
            paymentChart = initPaymentChart(paymentData);
            if (paymentChart) paymentChart.render();
        }
    });

    document.addEventListener('livewire:navigated', function() {
        var monthlySales = @json($monthlySales);
        var paymentData = @json($paymentMethodData);

        if (!document.querySelector("#salesChart")) return;

        if (salesChart) {
            salesChart.updateOptions({ series: [{ data: monthlySales }] });
        } else {
            salesChart = initSalesChart(monthlySales);
            salesChart.render();
        }

        if (paymentChart) {
            if (paymentData.length > 0) {
                paymentChart.updateOptions({
                    series: paymentData.map(d => d.value),
                    labels: paymentData.map(d => d.label),
                    colors: paymentData.map(d => d.color)
                });
            }
        } else if (paymentData.length > 0) {
            paymentChart = initPaymentChart(paymentData);
            if (paymentChart) paymentChart.render();
        }
    });

    // Listen for year changes from Livewire
    document.addEventListener('yearChanged', function(event) {
        if (salesChart) {
            salesChart.updateOptions({
                series: [{ data: event.detail.monthlySales }]
            });
        }
    });
</script>
@endpush
