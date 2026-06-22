<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected function modelClass(): string
    {
        return Product::class;
    }

    public function searchProducts(string $query, ?int $outletId = null): Collection
    {
        $q = Product::with(['category', 'unit'])
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->where('is_active', true);

        if ($outletId) {
            $q->where('outlet_id', $outletId);
        }

        return $q->limit(20)->get();
    }

    public function getLowStockProducts(int $outletId): Collection
    {
        return Product::with(['category', 'unit'])
            ->where('outlet_id', $outletId)
            ->whereColumn('stock', '<=', 'min_stock_alert')
            ->where('is_active', true)
            ->limit(10)
            ->get();
    }

    public function getTopSellingProducts(int $outletId, int $limit = 5): Collection
    {
        return Product::with(['category', 'unit'])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->withSum(['transactionDetails' => function ($q) {
                $q->whereHas('transaction', function ($t) {
                    $t->whereDate('transaction_date', today());
                });
            }], 'quantity')
            ->orderByDesc('transaction_details_sum_quantity')
            ->limit($limit)
            ->get();
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return Product::with(['category', 'unit'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
    }
}
