<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function searchProducts(string $query, ?int $outletId = null): Collection;
    public function getLowStockProducts(int $outletId): Collection;
    public function getTopSellingProducts(int $outletId, int $limit = 5): Collection;
    public function getProductsByCategory(int $categoryId): Collection;
}
