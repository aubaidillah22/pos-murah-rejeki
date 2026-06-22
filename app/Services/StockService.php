<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function increase(
        Product $product,
        int $quantity,
        string $type,
        ?Model $reference = null,
        ?string $description = null,
        ?int $userId = null,
        ?int $outletId = null,
    ): void {
        DB::transaction(function () use ($product, $quantity, $type, $reference, $description, $userId, $outletId) {
            $stockBefore = $product->stock;
            $product->increment('stock', $quantity);
            $product->refresh();

            $this->recordMovement(
                product: $product,
                quantityChange: $quantity,
                stockBefore: $stockBefore,
                type: $type,
                reference: $reference,
                description: $description,
                userId: $userId ?? auth()->id(),
                outletId: $outletId ?? auth()->user()?->outlet_id,
            );
        });
    }

    public function decrease(
        Product $product,
        int $quantity,
        string $type,
        ?Model $reference = null,
        ?string $description = null,
        ?int $userId = null,
        ?int $outletId = null,
    ): void {
        DB::transaction(function () use ($product, $quantity, $type, $reference, $description, $userId, $outletId) {
            $stockBefore = $product->stock;
            $product->decrement('stock', $quantity);
            $product->refresh();

            $this->recordMovement(
                product: $product,
                quantityChange: -$quantity,
                stockBefore: $stockBefore,
                type: $type,
                reference: $reference,
                description: $description,
                userId: $userId ?? auth()->id(),
                outletId: $outletId ?? auth()->user()?->outlet_id,
            );
        });
    }

    public function sync(
        Product $product,
        int $newStock,
        string $type,
        ?Model $reference = null,
        ?string $description = null,
        ?int $userId = null,
        ?int $outletId = null,
    ): void {
        DB::transaction(function () use ($product, $newStock, $type, $reference, $description, $userId, $outletId) {
            $stockBefore = $product->stock;
            $difference = $newStock - $stockBefore;

            $product->update(['stock' => $newStock]);

            $this->recordMovement(
                product: $product,
                quantityChange: $difference,
                stockBefore: $stockBefore,
                type: $type,
                reference: $reference,
                description: $description,
                userId: $userId ?? auth()->id(),
                outletId: $outletId ?? auth()->user()?->outlet_id,
            );
        });
    }

    public function setInitial(
        Product $product,
        int $stock,
        ?string $description = null,
        ?int $userId = null,
        ?int $outletId = null,
    ): void {
        $product->update(['stock' => $stock]);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $userId ?? auth()->id(),
            'outlet_id' => $outletId ?? auth()->user()?->outlet_id,
            'quantity_change' => $stock,
            'stock_before' => 0,
            'stock_after' => $stock,
            'type' => 'initial',
            'description' => $description ?? 'Stok awal produk',
        ]);
    }

    private function recordMovement(
        Product $product,
        int $quantityChange,
        int $stockBefore,
        string $type,
        ?Model $reference = null,
        ?string $description = null,
        ?int $userId = null,
        ?int $outletId = null,
    ): void {
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'outlet_id' => $outletId,
            'quantity_change' => $quantityChange,
            'stock_before' => $stockBefore,
            'stock_after' => $product->stock,
            'type' => $type,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->getKey(),
            'description' => $description,
        ]);
    }
}
