<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->json('variant_snapshot')
                ->nullable()
                ->after('variant_id')
                ->comment('Sipariş anındaki varyant bilgisi: renk, beden, fiyat vs.');

            $table->foreignId('product_seller_id')
                ->nullable()
                ->after('vendor_id')
                ->constrained('product_sellers')
                ->nullOnDelete();

            $table->decimal('unit_price', 10, 2)
                ->nullable()
                ->after('price')
                ->comment('Birim fiyat (price = unit_price * quantity)');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['product_seller_id']);
            $table->dropColumn(['variant_id', 'variant_snapshot', 'product_seller_id', 'unit_price']);
        });
    }
};
