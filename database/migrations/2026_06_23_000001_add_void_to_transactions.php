<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('voided_at')->nullable()->after('notes');
            $table->text('void_reason')->nullable()->after('voided_at');
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete()->after('void_reason');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['voided_at', 'void_reason', 'voided_by']);
        });
    }
};
