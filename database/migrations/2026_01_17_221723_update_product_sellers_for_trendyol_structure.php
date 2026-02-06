<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_sellers', function (Blueprint $table) {
            $table->dropUnique('product_vendors_product_id_vendor_id_unique');
        });

        Schema::table('product_sellers', function (Blueprint $table) {
            $table->foreignId('variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->string('seller_sku', 100)
                ->nullable()
                ->after('vendor_id');

            $table->decimal('sale_price', 10, 2)
                ->nullable()
                ->after('price');

            $table->boolean('free_shipping')
                ->default(false)
                ->after('shipping_type');

            $table->boolean('is_buybox_winner')
                ->default(false)
                ->after('is_featured');

            $table->unique(['product_id', 'variant_id', 'vendor_id'], 'product_sellers_unique');
        });

        Schema::table('product_sellers', function (Blueprint $table) {
            $table->dropIndex('product_vendors_vendor_id_stock_index');
            $table->index(['vendor_id', 'stock'], 'product_sellers_vendor_stock_index');
        });
    }

    public function down(): void
    {
        Schema::table('product_sellers', function (Blueprint $table) {
            $table->dropIndex('product_sellers_vendor_stock_index');
            $table->index(['vendor_id', 'stock'], 'product_vendors_vendor_id_stock_index');
        });

        Schema::table('product_sellers', function (Blueprint $table) {
            $table->dropUnique('product_sellers_unique');
            $table->dropForeign(['variant_id']);
            $table->dropColumn([
                'variant_id',
                'seller_sku',
                'sale_price',
                'free_shipping',
                'is_buybox_winner',
            ]);
            $table->unique(['product_id', 'vendor_id'], 'product_vendors_product_id_vendor_id_unique');
        });
    }
};
