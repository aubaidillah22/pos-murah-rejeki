<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default settings
        DB::table('settings')->insert([
            ['key' => 'default_tax', 'value' => '11', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_name', 'value' => config('app.name'), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_address', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_phone', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
