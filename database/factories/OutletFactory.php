<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutletFactory extends Factory
{
    protected $model = Outlet::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Cabang',
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'is_active' => true,
        ];
    }
}
