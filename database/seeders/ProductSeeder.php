<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $outletId = 1;
        $products = [
            ['Semen Gresik 50kg', 'Semen', 'Sak', 70000, 75000, 100, 20],
            ['Semen Tiga Roda 50kg', 'Semen', 'Sak', 72000, 77000, 80, 20],
            ['Semen Holcim 50kg', 'Semen', 'Sak', 68000, 73000, 60, 15],
            ['Cat Tembok Dulux 5kg', 'Cat', 'Kg', 120000, 145000, 50, 10],
            ['Cat Tembok Avitex 5kg', 'Cat', 'Kg', 85000, 105000, 40, 10],
            ['Cat Kayu Besi Nippon Paint 1kg', 'Cat', 'Kg', 45000, 55000, 30, 5],
            ['Cat Tembok Jotun 5kg', 'Cat', 'Kg', 130000, 155000, 25, 5],
            ['Besi Beton 10mm', 'Besi', 'Batang', 45000, 52000, 200, 30],
            ['Besi Beton 12mm', 'Besi', 'Batang', 60000, 69000, 150, 25],
            ['Besi Beton 8mm', 'Besi', 'Batang', 32000, 38000, 250, 30],
            ['Baja Ringan Canal C 0.75mm', 'Besi', 'Batang', 75000, 88000, 100, 15],
            ['Baja Ringan Reng 0.45mm', 'Besi', 'Batang', 35000, 42000, 100, 15],
            ['Wiremesh M8 2.1m x 5.4m', 'Besi', 'Lembar', 350000, 400000, 30, 5],
            ['Kayu Galian 6m', 'Kayu', 'Batang', 85000, 100000, 50, 10],
            ['Triplek 9mm 120x240cm', 'Kayu', 'Lembar', 95000, 115000, 40, 8],
            ['Triplek 12mm 120x240cm', 'Kayu', 'Lembar', 120000, 142000, 35, 8],
            ['Multiplek 15mm 120x240cm', 'Kayu', 'Lembar', 150000, 178000, 25, 5],
            ['Pipa PVC 3/4 inch', 'Pipa', 'Batang', 15000, 20000, 300, 50],
            ['Pipa PVC 1 inch', 'Pipa', 'Batang', 20000, 26000, 250, 40],
            ['Pipa PVC 2 inch', 'Pipa', 'Batang', 35000, 43000, 200, 30],
            ['Pipa PVC 3 inch', 'Pipa', 'Batang', 55000, 65000, 150, 20],
            ['Pipa PVC 4 inch', 'Pipa', 'Batang', 75000, 88000, 100, 15],
            ['Kabel NYM 2x1.5mm', 'Listrik', 'Meter', 3000, 4500, 1000, 100],
            ['Kabel NYM 3x2.5mm', 'Listrik', 'Meter', 7000, 9500, 800, 80],
            ['Kabel NYA 1.5mm', 'Listrik', 'Meter', 2000, 3200, 1000, 100],
            ['Saklar Broco 1 gang', 'Listrik', 'Unit', 8000, 12000, 150, 20],
            ['Stop Kontak Broco', 'Listrik', 'Unit', 10000, 15000, 150, 20],
            ['Lampu LED Philips 10W', 'Listrik', 'Unit', 25000, 35000, 100, 15],
            ['Lampu LED Philips 20W', 'Listrik', 'Unit', 40000, 55000, 80, 10],
            ['Palu Kayu 1kg', 'Alat', 'Unit', 30000, 40000, 30, 5],
            ['Obeng Set 6 in 1', 'Alat', 'Set', 25000, 35000, 40, 5],
            ['Meteran 5m', 'Alat', 'Unit', 15000, 22000, 50, 8],
            ['Gergaji Kayu 24 inch', 'Alat', 'Unit', 40000, 55000, 25, 5],
            ['Keramik Roman 40x40cm', 'Keramik', 'Dos', 55000, 68000, 200, 30],
            ['Keramik Asia Tile 40x40cm', 'Keramik', 'Dos', 45000, 55000, 150, 25],
            ['Genteng Tanah Liat', 'Atap', 'Biji', 3000, 4500, 1000, 100],
            ['Seng Gelombang 1.8m', 'Atap', 'Lembar', 45000, 55000, 200, 30],
            ['Seng Spandek 0.30mm 4m', 'Atap', 'Lembar', 80000, 95000, 100, 15],
            ['Asbes Gelombang Kecil 1.8m', 'Atap', 'Lembar', 55000, 68000, 80, 10],
            ['Bata Merah', 'Bata & Batako', 'Biji', 600, 900, 5000, 500],
            ['Batako Ringan', 'Bata & Batako', 'Biji', 3000, 4000, 1000, 100],
            ['Hebel AAC 7.5cm 60x20cm', 'Bata & Batako', 'M3', 600000, 720000, 50, 8],
            ['Pasir Beton', 'Pasir & Batu', 'M3', 250000, 320000, 30, 5],
            ['Batu Split 1/2', 'Pasir & Batu', 'M3', 200000, 260000, 30, 5],
            ['Plafon Gypsum 120x240cm', 'Plafon', 'Lembar', 55000, 70000, 60, 10],
            ['List Plafon PVC 4m', 'Plafon', 'Batang', 15000, 22000, 100, 15],
        ];

        foreach ($products as $product) {
            $category = \App\Models\Category::where('name', $product[1])->first();
            $unit = \App\Models\Unit::where('name', $product[2])->first();

            Product::create([
                'name' => $product[0],
                'sku' => 'SKU-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'category_id' => $category->id ?? null,
                'unit_id' => $unit->id ?? null,
                'purchase_price' => $product[3],
                'selling_price' => $product[4],
                'stock' => $product[5],
                'min_stock_alert' => $product[6],
                'outlet_id' => $outletId,
                'is_active' => true,
            ]);
        }
    }
}
