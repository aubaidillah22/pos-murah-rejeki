<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'receipt_width', 'value' => '80mm'],
            ['key' => 'receipt_show_logo', 'value' => '1'],
            ['key' => 'receipt_show_address', 'value' => '1'],
            ['key' => 'receipt_show_phone', 'value' => '1'],
            ['key' => 'receipt_show_tax', 'value' => '1'],
            ['key' => 'receipt_show_discount', 'value' => '1'],
            ['key' => 'receipt_show_payment_method', 'value' => '1'],
            ['key' => 'receipt_show_change', 'value' => '1'],
            ['key' => 'receipt_show_sku', 'value' => '0'],
            ['key' => 'receipt_header', 'value' => ''],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                ['value' => $s['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'receipt_width', 'receipt_show_logo', 'receipt_show_address',
            'receipt_show_phone', 'receipt_show_tax', 'receipt_show_discount',
            'receipt_show_payment_method', 'receipt_show_change', 'receipt_show_sku',
            'receipt_header',
        ])->delete();
    }
};
