<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['Sak', 'Kg', 'Batang', 'Lembar', 'Meter', 'Liter', 'Unit', 'Dos', 'Biji', 'Roll', 'Buah', 'Set', 'Zak', 'Ton', 'M3'];

        foreach ($units as $unit) {
            Unit::create(['name' => $unit]);
        }
    }
}
