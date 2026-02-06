<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('discount_price');
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('original_price');
            }
            if (!Schema::hasColumn('products', 'currency')) {
                $table->string('currency', 3)->default('TRY')->after('stock');
            }
            if (!Schema::hasColumn('products', 'shipping_time')) {
                $table->integer('shipping_time')->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount_price', 'original_price', 'stock', 'currency', 'shipping_time']);
        });
    }
};

