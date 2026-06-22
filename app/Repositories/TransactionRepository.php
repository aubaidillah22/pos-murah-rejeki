<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    protected function modelClass(): string
    {
        return Transaction::class;
    }

    public function getSalesByDateRange(string $startDate, string $endDate, ?int $outletId = null): Collection
    {
        $q = Transaction::with(['details.product', 'customer', 'user'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('payment_status', 'paid');

        if ($outletId) {
            $q->where('outlet_id', $outletId);
        }

        return $q->orderBy('transaction_date', 'desc')->get();
    }

    public function getDailySalesSummary(int $outletId): array
    {
        $today = today();
        
        $totalSales = Transaction::where('outlet_id', $outletId)
            ->whereDate('transaction_date', $today)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $totalTransaction = Transaction::where('outlet_id', $outletId)
            ->whereDate('transaction_date', $today)
            ->count();

        $totalProfit = Transaction::where('outlet_id', $outletId)
            ->whereDate('transaction_date', $today)
            ->where('payment_status', 'paid')
            ->sum(DB::raw('grand_total - total_amount'));

        return [
            'total_sales' => $totalSales,
            'total_transaction' => $totalTransaction,
            'total_profit' => $totalProfit,
        ];
    }

    public function getMonthlySales(int $year, ?int $outletId = null): Collection
    {
        $q = Transaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(grand_total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('transaction_date', $year)
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'));

        if ($outletId) {
            $q->where('outlet_id', $outletId);
        }

        return $q->get();
    }
}
