<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Semen', 'description' => 'Semen dan perekat bangunan'],
            ['name' => 'Cat', 'description' => 'Cat tembok, cat kayu, cat besi'],
            ['name' => 'Besi', 'description' => 'Besi beton, baja ringan, wiremesh'],
            ['name' => 'Kayu', 'description' => 'Kayu bangunan, triplek, multiplek'],
            ['name' => 'Pipa', 'description' => 'Pipa PVC, Pipa besi, fitting'],
            ['name' => 'Listrik', 'description' => 'Kabel, saklar, stop kontak, lampu'],
            ['name' => 'Alat', 'description' => 'Alat pertukangan dan perkakas'],
            ['name' => 'Keramik', 'description' => 'Keramik lantai dan dinding'],
            ['name' => 'Atap', 'description' => 'Genteng, seng, asbes, spandek'],
            ['name' => 'Bata & Batako', 'description' => 'Bata merah, batako, hebel'],
            ['name' => 'Pasir & Batu', 'description' => 'Pasir, batu split, sirtu'],
            ['name' => 'Plafon', 'description' => 'Plafon gypsum, GRC, list plafon'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'outlet_id' => 1,
                'is_active' => true,
            ]);
        }
    }
}
