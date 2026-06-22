<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $outletId = 1;

        // Admin
        $admin = User::create([
            'name' => 'Admin Murah Rejeki',
            'email' => 'admin@murahrejeki.com',
            'password' => bcrypt('password'),
            'outlet_id' => $outletId,
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Manager
        $manager = User::create([
            'name' => 'Manager Toko',
            'email' => 'manager@murahrejeki.com',
            'password' => bcrypt('password'),
            'outlet_id' => $outletId,
            'is_active' => true,
        ]);
        $manager->assignRole('Manager');

        // Kasir 1
        $kasir1 = User::create([
            'name' => 'Kasir Pagi',
            'email' => 'kasir1@murahrejeki.com',
            'password' => bcrypt('password'),
            'outlet_id' => $outletId,
            'is_active' => true,
        ]);
        $kasir1->assignRole('Kasir');

        // Kasir 2
        $kasir2 = User::create([
            'name' => 'Kasir Sore',
            'email' => 'kasir2@murahrejeki.com',
            'password' => bcrypt('password'),
            'outlet_id' => $outletId,
            'is_active' => true,
        ]);
        $kasir2->assignRole('Kasir');
    }
}
