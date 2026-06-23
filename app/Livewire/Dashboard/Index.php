<?php

namespace App\Livewire\Dashboard;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Customer;
use App\Services\ReportService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $totalSalesToday = 0;
    public $totalTransactionsToday = 0;
    public $totalProducts = 0;
    public $totalCustomers = 0;
    public $profitToday = 0;
    public $lowStockProducts = [];
    public $topSellingProducts = [];
    public $monthlySales = [];
    public $paymentMethodData = [];
    public $recentTransactions = [];
    public $topCashierToday = [];

    public $selectedYear;
    public $outletFilter;
    public $outlets = [];

    public function mount()
    {
        $this->selectedYear = now()->year;
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            $this->outlets = Outlet::where('is_active', true)->orderBy('name')->get();
            $this->outletFilter = $user->outlet_id ?? '';
        } else {
            $this->outletFilter = $user->outlet_id;
        }

        $this->loadData();
    }

    public function updatedSelectedYear()
    {
        $this->loadData();
        $this->dispatch('chartChanged', monthlySales: $this->monthlySales, paymentMethodData: $this->paymentMethodData);
    }

    public function updatedOutletFilter()
    {
        $this->loadData();
        $this->dispatch('chartChanged', monthlySales: $this->monthlySales, paymentMethodData: $this->paymentMethodData);
    }

    public function loadData()
    {
        $user = auth()->user();
        $outletId = $user->hasRole('Admin') ? ($this->outletFilter ?: null) : $user->outlet_id;
        $today = now()->format('Y-m-d');

        // Total penjualan hari ini (exclude voided)
        $this->totalSalesToday = Transaction::whereDate('transaction_date', $today)
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->sum('grand_total');

        // Jumlah transaksi hari ini (exclude voided)
        $this->totalTransactionsToday = Transaction::whereDate('transaction_date', $today)
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->count();

        // Laba kotor hari ini (exclude voided) — pakai subquery agar tidak ambiguous
        $profitQuery = Transaction::whereDate('transactions.transaction_date', $today)
            ->where('transactions.payment_status', 'paid')
            ->whereNull('transactions.voided_at')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id');

        if ($outletId) {
            $profitQuery->where('transactions.outlet_id', $outletId);
        }

        $this->profitToday = $profitQuery->sum(DB::raw('transaction_details.quantity * (products.selling_price - products.purchase_price)'));

        // Total produk aktif
        $this->totalProducts = Product::when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('is_active', true)->count();

        // Total pelanggan
        $this->totalCustomers = Customer::count();

        // Stok menipis
        $this->lowStockProducts = Product::with(['category', 'unit'])
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->whereColumn('stock', '<=', 'min_stock_alert')
            ->where('is_active', true)
            ->where('min_stock_alert', '>', 0)
            ->limit(10)
            ->get();

        // Produk terlaris hari ini
        $this->topSellingProducts = Product::with(['category', 'unit'])
            ->select('products.*', DB::raw('COALESCE(SUM(td.quantity), 0) as total_sold'))
            ->leftJoin('transaction_details as td', 'products.id', '=', 'td.product_id')
            ->leftJoin('transactions as t', function ($join) use ($today) {
                $join->on('td.transaction_id', '=', 't.id')
                    ->whereDate('t.transaction_date', $today)
                    ->where('t.payment_status', 'paid')
                    ->whereNull('t.voided_at');
            })
            ->when($outletId, fn($q) => $q->where('products.outlet_id', $outletId))
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Grafik penjualan bulanan (per year yang dipilih, exclude voided)
        $year = $this->selectedYear;
        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', transaction_date) AS INTEGER)"
            : "MONTH(transaction_date)";
        $monthlyData = Transaction::select(
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(grand_total) as total')
            )
            ->whereYear('transaction_date', $year)
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->groupBy(DB::raw($monthExpr))
            ->orderBy(DB::raw($monthExpr))
            ->pluck('total', 'month')
            ->toArray();

        $this->monthlySales = [];
        for ($i = 1; $i <= 12; $i++) {
            $this->monthlySales[] = (int) ($monthlyData[$i] ?? 0);
        }

        // Distribusi metode pembayaran (hari ini, exclude voided)
        $paymentMethods = Transaction::select('payment_method', DB::raw('SUM(grand_total) as total'))
            ->whereDate('transaction_date', $today)
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'debit' => 'Debit'];
        $methodColors = ['cash' => '#059669', 'qris' => '#3b82f6', 'transfer' => '#f59e0b', 'debit' => '#8b5cf6'];
        $this->paymentMethodData = [];
        foreach ($paymentMethods as $method => $total) {
            $this->paymentMethodData[] = [
                'label' => $methodLabels[$method] ?? $method,
                'value' => (int) $total,
                'color' => $methodColors[$method] ?? '#6b7280',
            ];
        }

        // Transaksi terbaru (5 terakhir, exclude voided)
        $this->recentTransactions = Transaction::with(['customer', 'user'])
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Top kasir hari ini (exclude voided)
        $this->topCashierToday = Transaction::select('user_id', DB::raw('COUNT(*) as total_trans'), DB::raw('SUM(grand_total) as total_amount'))
            ->whereDate('transaction_date', $today)
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('transactions.outlet_id', $outletId))
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->with('user')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
