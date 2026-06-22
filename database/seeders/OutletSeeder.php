<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        Outlet::create([
            'name' => 'Murah Rejeki Pusat',
            'address' => 'Jl. Raya Utama No. 123, Jakarta',
            'phone' => '021-12345678',
            'email' => 'pusat@murahrejeki.com',
            'is_active' => true,
        ]);
    }
}
