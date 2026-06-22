<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'barcode' => fake()->unique()->ean13(),
            'purchase_price' => fake()->numberBetween(1000, 50000),
            'selling_price' => fake()->numberBetween(5000, 100000),
            'stock' => fake()->numberBetween(10, 500),
            'min_stock_alert' => fake()->numberBetween(5, 20),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
