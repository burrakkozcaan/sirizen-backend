<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('custom_commission_rate', 5, 2)
                ->nullable()
                ->after('original_price')
                ->comment('Ürün bazlı özel komisyon oranı (%) - Kategori komisyonunu override eder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('custom_commission_rate');
        });
    }
};
