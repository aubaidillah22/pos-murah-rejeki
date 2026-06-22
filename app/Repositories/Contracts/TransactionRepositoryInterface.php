<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getSalesByDateRange(string $startDate, string $endDate, ?int $outletId = null): Collection;
    public function getDailySalesSummary(int $outletId): array;
    public function getMonthlySales(int $year, ?int $outletId = null): Collection;
}
