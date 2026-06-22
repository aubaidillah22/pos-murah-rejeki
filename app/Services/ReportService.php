<?php

namespace App\Services;

use App\Models\CashFlow;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSalesReport(string $startDate, string $endDate, ?int $outletId = null, ?int $userId = null): Collection
    {
        $q = Transaction::with(['details.product.category', 'customer', 'user', 'outlet'])
            ->whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->whereNull('voided_at');

        if ($outletId) {
            $q->where('outlet_id', $outletId);
        }

        if ($userId) {
            $q->where('user_id', $userId);
        }

        return $q->orderBy('transaction_date', 'desc')->get();
    }

    public function getProfitLoss(string $startDate, string $endDate, ?int $outletId = null): array
    {
        // Total penjualan (pendapatan, exclude voided)
        $totalSales = Transaction::whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->sum('grand_total');

        // Harga pokok penjualan (HPP, exclude voided)
        $hpp = Transaction::whereBetween('transaction_date', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->whereNull('voided_at')
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->sum(DB::raw('transaction_details.quantity * products.purchase_price'));

        // Total pengeluaran
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->sum('amount');

        $grossProfit = $totalSales - $hpp;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'total_sales' => $totalSales,
            'hpp' => $hpp,
            'gross_profit' => $grossProfit,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
        ];
    }

    public function getCashFlow(string $startDate, string $endDate, ?int $outletId = null): Collection
    {
        return CashFlow::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->when($outletId, function ($q) use ($outletId) {
                // Cash flow melalui transaksi, filter outlet
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStockReport(?int $outletId = null): Collection
    {
        return Product::with(['category', 'unit'])
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getTopProductsByCategory(?int $outletId = null, ?int $limit = 10): Collection
    {
        return Product::with(['category', 'unit'])
            ->select('products.*', DB::raw('SUM(transaction_details.quantity) as total_sold'))
            ->join('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.payment_status', 'paid')
            ->when($outletId, fn($q) => $q->where('products.outlet_id', $outletId))
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }
}
