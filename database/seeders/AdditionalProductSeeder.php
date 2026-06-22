<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class AdditionalProductSeeder extends Seeder
{
    public function run(): void
    {
        $outletId = 1;

        $categoryMap = Category::pluck('id', 'name');
        $unitMap = Unit::pluck('id', 'name');

        $products = [
            // Semen
            ['name' => 'Semen Putih Tiga Roda 40kg', 'category' => 'Semen', 'unit' => 'Sak', 'purchase' => 55000, 'selling' => 65000, 'stock' => 50, 'min' => 10],
            ['name' => 'Semen Mortar MU-100 40kg', 'category' => 'Semen', 'unit' => 'Zak', 'purchase' => 48000, 'selling' => 58000, 'stock' => 60, 'min' => 12],
            ['name' => 'Semen Mortar MU-200 40kg', 'category' => 'Semen', 'unit' => 'Zak', 'purchase' => 52000, 'selling' => 62000, 'stock' => 45, 'min' => 10],
            ['name' => 'Semen Portland 50kg', 'category' => 'Semen', 'unit' => 'Sak', 'purchase' => 65000, 'selling' => 72000, 'stock' => 70, 'min' => 15],
            ['name' => 'Semen Dynamix 50kg', 'category' => 'Semen', 'unit' => 'Sak', 'purchase' => 72000, 'selling' => 80000, 'stock' => 40, 'min' => 8],
            ['name' => 'Semen SCG 50kg', 'category' => 'Semen', 'unit' => 'Sak', 'purchase' => 68000, 'selling' => 75000, 'stock' => 55, 'min' => 10],

            // Cat
            ['name' => 'Cat Tembok Mowilex 5kg', 'category' => 'Cat', 'unit' => 'Kg', 'purchase' => 110000, 'selling' => 135000, 'stock' => 30, 'min' => 5],
            ['name' => 'Cat Tembok Vinilex 5kg', 'category' => 'Cat', 'unit' => 'Kg', 'purchase' => 90000, 'selling' => 110000, 'stock' => 35, 'min' => 5],
            ['name' => 'Cat Minyak Nippon Paint 1kg', 'category' => 'Cat', 'unit' => 'Kg', 'purchase' => 40000, 'selling' => 50000, 'stock' => 40, 'min' => 8],
            ['name' => 'Plamir Tembok 1kg', 'category' => 'Cat', 'unit' => 'Kg', 'purchase' => 12000, 'selling' => 18000, 'stock' => 80, 'min' => 15],
            ['name' => 'Cat Tembok Dulux 2.5L', 'category' => 'Cat', 'unit' => 'Liter', 'purchase' => 75000, 'selling' => 95000, 'stock' => 25, 'min' => 5],
            ['name' => 'Cat Tembok Avitex 25kg', 'category' => 'Cat', 'unit' => 'Kg', 'purchase' => 320000, 'selling' => 380000, 'stock' => 15, 'min' => 3],

            // Besi
            ['name' => 'Besi Beton 6mm', 'category' => 'Besi', 'unit' => 'Batang', 'purchase' => 25000, 'selling' => 32000, 'stock' => 300, 'min' => 50],
            ['name' => 'Besi Beton 16mm', 'category' => 'Besi', 'unit' => 'Batang', 'purchase' => 95000, 'selling' => 112000, 'stock' => 80, 'min' => 15],
            ['name' => 'Baja Ringan Canal C 0.65mm', 'category' => 'Besi', 'unit' => 'Batang', 'purchase' => 60000, 'selling' => 72000, 'stock' => 120, 'min' => 20],
            ['name' => 'Baja Ringan Reng 0.35mm', 'category' => 'Besi', 'unit' => 'Batang', 'purchase' => 28000, 'selling' => 35000, 'stock' => 150, 'min' => 25],
            ['name' => 'Kawat Bendrat 1kg', 'category' => 'Besi', 'unit' => 'Kg', 'purchase' => 15000, 'selling' => 20000, 'stock' => 100, 'min' => 20],
            ['name' => 'Paku Beton 5cm', 'category' => 'Besi', 'unit' => 'Kg', 'purchase' => 18000, 'selling' => 25000, 'stock' => 80, 'min' => 15],

            // Kayu
            ['name' => 'Kayu Balok 5x10cm 4m', 'category' => 'Kayu', 'unit' => 'Batang', 'purchase' => 45000, 'selling' => 55000, 'stock' => 80, 'min' => 15],
            ['name' => 'Kayu Balok 6x12cm 4m', 'category' => 'Kayu', 'unit' => 'Batang', 'purchase' => 65000, 'selling' => 78000, 'stock' => 60, 'min' => 10],
            ['name' => 'Kayu Kaso 4x6cm 4m', 'category' => 'Kayu', 'unit' => 'Batang', 'purchase' => 28000, 'selling' => 35000, 'stock' => 150, 'min' => 25],
            ['name' => 'Multiplek 18mm 120x240cm', 'category' => 'Kayu', 'unit' => 'Lembar', 'purchase' => 175000, 'selling' => 210000, 'stock' => 20, 'min' => 4],
            ['name' => 'Partikel Board 12mm 120x240cm', 'category' => 'Kayu', 'unit' => 'Lembar', 'purchase' => 85000, 'selling' => 105000, 'stock' => 30, 'min' => 5],
            ['name' => 'Hardboard 3mm 120x240cm', 'category' => 'Kayu', 'unit' => 'Lembar', 'purchase' => 50000, 'selling' => 65000, 'stock' => 40, 'min' => 8],

            // Pipa
            ['name' => 'Pipa PVC 1/2 inch', 'category' => 'Pipa', 'unit' => 'Batang', 'purchase' => 10000, 'selling' => 14000, 'stock' => 350, 'min' => 50],
            ['name' => 'Pipa PVC 5 inch', 'category' => 'Pipa', 'unit' => 'Batang', 'purchase' => 95000, 'selling' => 115000, 'stock' => 80, 'min' => 15],
            ['name' => 'Pipa PVC 6 inch', 'category' => 'Pipa', 'unit' => 'Batang', 'purchase' => 120000, 'selling' => 145000, 'stock' => 50, 'min' => 10],
            ['name' => 'Lem PVC', 'category' => 'Pipa', 'unit' => 'Liter', 'purchase' => 25000, 'selling' => 35000, 'stock' => 60, 'min' => 10],
            ['name' => 'Sambungan PVC 3/4 inch', 'category' => 'Pipa', 'unit' => 'Unit', 'purchase' => 3000, 'selling' => 5000, 'stock' => 400, 'min' => 50],
            ['name' => 'Sambungan PVC 1 inch', 'category' => 'Pipa', 'unit' => 'Unit', 'purchase' => 4000, 'selling' => 6000, 'stock' => 350, 'min' => 40],

            // Listrik
            ['name' => 'Kabel NYM 2x2.5mm', 'category' => 'Listrik', 'unit' => 'Meter', 'purchase' => 5000, 'selling' => 7000, 'stock' => 800, 'min' => 80],
            ['name' => 'Kabel NYM 3x1.5mm', 'category' => 'Listrik', 'unit' => 'Meter', 'purchase' => 5500, 'selling' => 7500, 'stock' => 700, 'min' => 70],
            ['name' => 'Kabel NYA 2.5mm', 'category' => 'Listrik', 'unit' => 'Meter', 'purchase' => 3500, 'selling' => 5000, 'stock' => 900, 'min' => 80],
            ['name' => 'Lampu LED Philips 5W', 'category' => 'Listrik', 'unit' => 'Unit', 'purchase' => 18000, 'selling' => 25000, 'stock' => 120, 'min' => 20],
            ['name' => 'Lampu LED Philips 30W', 'category' => 'Listrik', 'unit' => 'Unit', 'purchase' => 55000, 'selling' => 72000, 'stock' => 60, 'min' => 10],
            ['name' => 'Fiting Lampu', 'category' => 'Listrik', 'unit' => 'Unit', 'purchase' => 4000, 'selling' => 7000, 'stock' => 200, 'min' => 30],
            ['name' => 'MCB 6A', 'category' => 'Listrik', 'unit' => 'Unit', 'purchase' => 25000, 'selling' => 35000, 'stock' => 80, 'min' => 15],

            // Alat
            ['name' => 'Palu Bodem 0.5kg', 'category' => 'Alat', 'unit' => 'Unit', 'purchase' => 25000, 'selling' => 35000, 'stock' => 40, 'min' => 8],
            ['name' => 'Obeng Set 12 in 1', 'category' => 'Alat', 'unit' => 'Set', 'purchase' => 45000, 'selling' => 60000, 'stock' => 25, 'min' => 5],
            ['name' => 'Tang Kombinasi', 'category' => 'Alat', 'unit' => 'Unit', 'purchase' => 35000, 'selling' => 48000, 'stock' => 30, 'min' => 5],
            ['name' => 'Kunci Inggris 12 inch', 'category' => 'Alat', 'unit' => 'Unit', 'purchase' => 55000, 'selling' => 72000, 'stock' => 20, 'min' => 4],
            ['name' => 'Waterpass 60cm', 'category' => 'Alat', 'unit' => 'Unit', 'purchase' => 20000, 'selling' => 30000, 'stock' => 35, 'min' => 5],
            ['name' => 'Rol Cat 4 inch', 'category' => 'Alat', 'unit' => 'Unit', 'purchase' => 12000, 'selling' => 18000, 'stock' => 60, 'min' => 10],

            // Keramik
            ['name' => 'Keramik Roman 60x60cm', 'category' => 'Keramik', 'unit' => 'Dos', 'purchase' => 95000, 'selling' => 120000, 'stock' => 100, 'min' => 15],
            ['name' => 'Keramik Asia Tile 30x30cm', 'category' => 'Keramik', 'unit' => 'Dos', 'purchase' => 35000, 'selling' => 45000, 'stock' => 180, 'min' => 25],
            ['name' => 'Keramik Mulia 40x40cm', 'category' => 'Keramik', 'unit' => 'Dos', 'purchase' => 48000, 'selling' => 60000, 'stock' => 130, 'min' => 20],
            ['name' => 'Granit Roman 60x60cm', 'category' => 'Keramik', 'unit' => 'Dos', 'purchase' => 130000, 'selling' => 165000, 'stock' => 60, 'min' => 10],
            ['name' => 'Semen Nat Keramik 1kg', 'category' => 'Keramik', 'unit' => 'Kg', 'purchase' => 8000, 'selling' => 12000, 'stock' => 150, 'min' => 25],
            ['name' => 'Keramik Dinding 25x40cm', 'category' => 'Keramik', 'unit' => 'Dos', 'purchase' => 55000, 'selling' => 70000, 'stock' => 80, 'min' => 12],

            // Atap
            ['name' => 'Genteng Metal Pasir', 'category' => 'Atap', 'unit' => 'Biji', 'purchase' => 35000, 'selling' => 45000, 'stock' => 500, 'min' => 50],
            ['name' => 'Seng Spandek 0.35mm 4m', 'category' => 'Atap', 'unit' => 'Lembar', 'purchase' => 95000, 'selling' => 115000, 'stock' => 80, 'min' => 12],
            ['name' => 'Nok Genteng', 'category' => 'Atap', 'unit' => 'Biji', 'purchase' => 8000, 'selling' => 12000, 'stock' => 300, 'min' => 30],
            ['name' => 'Baut Roofing', 'category' => 'Atap', 'unit' => 'Buah', 'purchase' => 1500, 'selling' => 2500, 'stock' => 1000, 'min' => 100],
            ['name' => 'Talang PVC 4 inch', 'category' => 'Atap', 'unit' => 'Batang', 'purchase' => 40000, 'selling' => 52000, 'stock' => 60, 'min' => 10],
            ['name' => 'Seng Gelombang 2.1m', 'category' => 'Atap', 'unit' => 'Lembar', 'purchase' => 55000, 'selling' => 68000, 'stock' => 150, 'min' => 20],

            // Bata & Batako
            ['name' => 'Hebel AAC 10cm 60x20cm', 'category' => 'Bata & Batako', 'unit' => 'M3', 'purchase' => 650000, 'selling' => 780000, 'stock' => 35, 'min' => 5],
            ['name' => 'Bata Ringan Citicon 7.5cm', 'category' => 'Bata & Batako', 'unit' => 'M3', 'purchase' => 580000, 'selling' => 700000, 'stock' => 40, 'min' => 6],
            ['name' => 'Semen Mortar MU-380', 'category' => 'Bata & Batako', 'unit' => 'Zak', 'purchase' => 50000, 'selling' => 62000, 'stock' => 50, 'min' => 10],
            ['name' => 'Wiremesh Dinding M4', 'category' => 'Bata & Batako', 'unit' => 'Lembar', 'purchase' => 80000, 'selling' => 100000, 'stock' => 40, 'min' => 8],
            ['name' => 'Bata Ekspos Merah', 'category' => 'Bata & Batako', 'unit' => 'Biji', 'purchase' => 1200, 'selling' => 1800, 'stock' => 3000, 'min' => 300],
            ['name' => 'Lem Bata Ringan 40kg', 'category' => 'Bata & Batako', 'unit' => 'Zak', 'purchase' => 45000, 'selling' => 55000, 'stock' => 40, 'min' => 8],

            // Pasir & Batu
            ['name' => 'Pasir Pasang', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 180000, 'selling' => 240000, 'stock' => 25, 'min' => 4],
            ['name' => 'Sirtu (Pasir Batu)', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 150000, 'selling' => 200000, 'stock' => 20, 'min' => 4],
            ['name' => 'Batu Kali Belah', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 200000, 'selling' => 260000, 'stock' => 15, 'min' => 3],
            ['name' => 'Pasir Urug', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 120000, 'selling' => 165000, 'stock' => 30, 'min' => 5],
            ['name' => 'Batu Koral Cor 1/2', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 220000, 'selling' => 280000, 'stock' => 20, 'min' => 3],
            ['name' => 'Pasir Silika', 'category' => 'Pasir & Batu', 'unit' => 'M3', 'purchase' => 250000, 'selling' => 320000, 'stock' => 12, 'min' => 2],

            // Plafon
            ['name' => 'Plafon PVC 120x240cm', 'category' => 'Plafon', 'unit' => 'Lembar', 'purchase' => 65000, 'selling' => 82000, 'stock' => 50, 'min' => 8],
            ['name' => 'Rangka Plafon Hollow 4m', 'category' => 'Plafon', 'unit' => 'Batang', 'purchase' => 18000, 'selling' => 25000, 'stock' => 200, 'min' => 30],
            ['name' => 'Rangka Plafon Galvalum 4m', 'category' => 'Plafon', 'unit' => 'Batang', 'purchase' => 22000, 'selling' => 30000, 'stock' => 150, 'min' => 20],
            ['name' => 'Compound Gypsum 1kg', 'category' => 'Plafon', 'unit' => 'Kg', 'purchase' => 8000, 'selling' => 12000, 'stock' => 100, 'min' => 15],
            ['name' => 'Wall Angle 3m', 'category' => 'Plafon', 'unit' => 'Batang', 'purchase' => 12000, 'selling' => 18000, 'stock' => 120, 'min' => 20],
            ['name' => 'Skrup Gypsum', 'category' => 'Plafon', 'unit' => 'Buah', 'purchase' => 500, 'selling' => 1000, 'stock' => 2000, 'min' => 200],
        ];

        $stockService = app(StockService::class);

        $skuCounter = Product::max('id') + 1;

        foreach ($products as $product) {
            $category = $categoryMap[$product['category']] ?? null;
            $unit = $unitMap[$product['unit']] ?? null;

            $p = Product::create([
                'name' => $product['name'],
                'sku' => 'SKU-' . str_pad($skuCounter++, 4, '0', STR_PAD_LEFT),
                'category_id' => $category,
                'unit_id' => $unit,
                'purchase_price' => $product['purchase'],
                'selling_price' => $product['selling'],
                'stock' => 0,
                'min_stock_alert' => $product['min'],
                'outlet_id' => $outletId,
                'is_active' => true,
            ]);

            if ($product['stock'] > 0) {
                $stockService->setInitial(
                    product: $p,
                    stock: $product['stock'],
                    description: 'Stok awal ' . $product['name'],
                    outletId: $outletId,
                );
            }
        }

        $this->command->info(count($products) . ' produk baru berhasil ditambahkan!');
    }
}
