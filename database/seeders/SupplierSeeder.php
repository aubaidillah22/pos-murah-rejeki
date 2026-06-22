<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT. Semen Indonesia', 'contact_person' => 'Bapak Andi', 'phone' => '031-1234567', 'email' => 'sales@semenindonesia.com', 'address' => 'Jl. Raya Semen No. 1, Gresik'],
            ['name' => 'PT. Nippon Paint Indonesia', 'contact_person' => 'Ibu Dewi', 'phone' => '021-7654321', 'email' => 'sales@nipponpaint.com', 'address' => 'Jl. Industri No. 55, Jakarta'],
            ['name' => 'PT. Krakatau Steel', 'contact_person' => 'Bapak Rudi', 'phone' => '022-555888', 'email' => 'sales@krakatausteel.com', 'address' => 'Jl. Besi Raya, Cilegon'],
            ['name' => 'UD. Kayu Jaya Abadi', 'contact_person' => 'Bapak Tono', 'phone' => '024-333444', 'email' => 'kayu@jayaabadi.com', 'address' => 'Jl. Kayu Manis No. 22, Semarang'],
            ['name' => 'PT. Pipa Unindo', 'contact_person' => 'Ibu Sari', 'phone' => '021-444555', 'email' => 'sales@pipaunindo.com', 'address' => 'Jl. Pipa No. 88, Tangerang'],
            ['name' => 'PT. Philips Indonesia', 'contact_person' => 'Bapak Hendra', 'phone' => '021-666777', 'email' => 'sales@philips.com', 'address' => 'Jl. Elektronik No. 10, Jakarta'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
