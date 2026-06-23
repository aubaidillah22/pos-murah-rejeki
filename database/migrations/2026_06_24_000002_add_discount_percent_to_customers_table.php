<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('is_member');
        });

        // Remove global member_discount setting, now per-customer
        Setting::where('key', 'member_discount')->delete();
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};
