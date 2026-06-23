<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="card p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="p-3 animate-fade-in" style="animation-delay: 0s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Penjualan Hari Ini</p>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 tabular-nums mt-1">Rp {{ number_format($totalSalesToday, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-3 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Transaksi Hari Ini</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 tabular-nums mt-1">{{ $totalTransactionsToday }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-3 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Laba Kotor Hari Ini</p>
                        <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 tabular-nums mt-1">Rp {{ number_format($profitToday, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg shadow-green-200 dark:shadow-green-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-3 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Produk</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 tabular-nums mt-1">{{ $totalProducts }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-amber-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-3 animate-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Pelanggan</p>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 tabular-nums mt-1">{{ $totalCustomers }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Penjualan Bulanan -->
        <div class="lg:col-span-2 card p-5 animate-fade-in" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100"><span class="text-emerald-600">Grafik</span> Penjualan Bulanan</h3>
                <div class="flex items-center gap-2">
                    @if(count($outlets) > 0)
                    <select wire:model.live="outletFilter" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Outlet</option>
                        @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                    @endif
                    <select wire:model.live="selectedYear" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div id="salesChart" style="min-height: 300px;"></div>
        </div>

        <!-- Pie Chart Metode Pembayaran -->
        <div class="card p-5 animate-fade-in" style="animation-delay: 0.6s">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4"><span class="text-blue-600">Metode</span> Pembayaran Hari Ini</h3>
            @if(count($paymentMethodData) > 0)
            <div id="paymentChart" style="min-height: 250px;"></div>
            <div class="mt-4 space-y-2.5">
                @foreach($paymentMethodData as $pm)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background: {{ $pm['color'] }}"></span>
                        <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $pm['label'] }}</span>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-gray-100 tabular-nums">Rp {{ number_format($pm['value'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <svg class="w-12 h-12 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Belum ada transaksi hari ini</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Row 2: Top Products + Recent Transactions + Top Cashier -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Produk Terlaris -->
        <div class="card p-5 animate-fade-in" style="animation-delay: 0.7s">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </span>
                Produk Terlaris Hari Ini
            </h3>
            <div class="space-y-2">
                @forelse($topSellingProducts as $index => $product)
                <div class="flex items-center justify-between p-2.5 rounded-lg {{ $index == 0 ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }} transition-colors">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <span class="w-7 h-7 rounded-full {{ $index == 0 ? 'bg-amber-400 text-white' : ($index == 1 ? 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300' : ($index == 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')) }} flex items-center justify-center text-xs font-bold shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $product->category?->name }} · {{ $product->unit?->name }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums ml-2">{{ $product->total_sold }}</span>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-sm">Belum ada penjualan hari ini</div>
                @endforelse
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="card p-5 animate-fade-in" style="animation-delay: 0.8s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    Transaksi Terbaru
                </h3>
                <a href="{{ route('transactions') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Lihat Semua</a>
            </div>
            <div class="space-y-2">
                @forelse($recentTransactions as $t)
                <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $t->invoice_number }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $t->customer?->name ?? 'Umum' }} — {{ $t->transaction_date->format('H:i') }}
                        </p>
                    </div>
                    <div class="text-right ml-2">
                        <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</p>
                        <p class="text-xs {{ $t->payment_status === 'paid' ? 'text-green-500' : 'text-amber-500' }}">
                            {{ $t->payment_status === 'paid' ? 'Lunas' : 'Piutang' }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-sm">Belum ada transaksi</div>
                @endforelse
            </div>
        </div>

        <!-- Top Kasir -->
        <div class="card p-5 animate-fade-in" style="animation-delay: 0.9s">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Kinerja Kasir Hari Ini
            </h3>
            <div class="space-y-2">
                @forelse($topCashierToday as $index => $cashier)
                <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-xs font-bold text-white shadow-sm shrink-0">
                            {{ substr($cashier->user?->name ?? '?', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $cashier->user?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $cashier->total_trans }} transaksi</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">Rp {{ number_format($cashier->total_amount, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-sm">Belum ada transaksi hari ini</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Notifikasi Stok Menipis -->
    <div class="card p-5 animate-fade-in" style="animation-delay: 1s">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-900/50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </span>
                Notifikasi Stok Menipis
            </h3>
            <a href="{{ route('products') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Lihat Semua</a>
        </div>
        @if($lowStockProducts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($lowStockProducts as $product)
            <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/20 rounded-xl p-3.5 border border-red-100 dark:border-red-800">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Stok: <span class="font-semibold tabular-nums">{{ $product->stock }}</span> {{ $product->unit?->name }} (Min: {{ $product->min_stock_alert }})</p>
                </div>
                <a href="{{ route('purchases') }}" class="shrink-0 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors ml-2">Pesan</a>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex items-center justify-center gap-2 py-6 text-sm text-gray-400 dark:text-gray-500">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Semua stok dalam kondisi aman
        </div>
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
                type: 'line',
                height: 300,
                toolbar: { show: false },
                foreColor: '#6b7280',
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 1000,
                    dynamicAnimation: { speed: 500 }
                },
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 0,
                    blur: 8,
                    color: '#059669',
                    opacity: 0.2
                }
            },
            colors: ['#059669'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            markers: {
                size: 5,
                colors: ['#fff'],
                strokeColors: '#059669',
                strokeWidth: 3,
                hover: { size: 7 }
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                axisBorder: { show: false },
                axisTicks: { show: false },
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
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    gradientToColors: ['#34d399'],
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            }
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

    // Listen for filter changes from Livewire (year or outlet)
    document.addEventListener('chartChanged', function(event) {
        if (salesChart) {
            salesChart.updateOptions({
                series: [{ data: event.detail.monthlySales }]
            });
        }
        if (paymentChart && event.detail.paymentMethodData) {
            var data = event.detail.paymentMethodData;
            if (data.length > 0) {
                paymentChart.updateOptions({
                    series: data.map(function(d) { return d.value; }),
                    labels: data.map(function(d) { return d.label; }),
                    colors: data.map(function(d) { return d.color; })
                });
            }
        } else if (event.detail.paymentMethodData && event.detail.paymentMethodData.length === 0 && paymentChart) {
            paymentChart.destroy();
            paymentChart = null;
        }
    });
</script>
@endpush
