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
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('size');
            }
            if (!Schema::hasColumn('product_variants', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('product_variants', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('sale_price');
            }
            if (!Schema::hasColumn('product_variants', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('stock');
            }
            if (!Schema::hasColumn('product_variants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_default');
            }
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('product_variants', 'value')) {
                $table->string('value')->nullable()->after('weight'); // For display: "S", "M", "Red", etc.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['price', 'sale_price', 'original_price', 'is_default', 'is_active', 'weight', 'value']);
        });
    }
};

