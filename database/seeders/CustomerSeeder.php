<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Pelanggan Umum', 'phone' => '-', 'email' => null, 'address' => null, 'is_member' => false],
            ['name' => 'Budi Santoso', 'phone' => '081234567890', 'email' => 'budi@email.com', 'address' => 'Jl. Merdeka No. 45', 'is_member' => true],
            ['name' => 'PT. Bangun Karya', 'phone' => '021-98765432', 'email' => 'info@bangunkarya.com', 'address' => 'Jl. Industri Raya No. 10', 'is_member' => true],
            ['name' => 'CV. Maju Bersama', 'phone' => '08119876543', 'email' => 'maju@bersama.com', 'address' => 'Jl. Raya Selatan No. 78', 'is_member' => true],
            ['name' => 'Siti Rahmawati', 'phone' => '085612345678', 'email' => null, 'address' => 'Perum Griya Indah Blok A5', 'is_member' => false],
            ['name' => 'Ahmad Hidayat', 'phone' => '087812349876', 'email' => 'ahmad@hidayat.com', 'address' => 'Jl. Kembang No. 12', 'is_member' => true],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
