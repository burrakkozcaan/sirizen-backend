<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cart_items')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'product_vendor_id')) {
                return;
            }

            $table->dropUnique(['cart_id', 'product_vendor_id']);
            $table->dropForeign(['product_vendor_id']);
            $table->renameColumn('product_vendor_id', 'product_seller_id');
            $table->foreign('product_seller_id')
                ->references('id')
                ->on('product_sellers')
                ->cascadeOnDelete();
            $table->unique(['cart_id', 'product_seller_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cart_items')) {
            return;
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'product_seller_id')) {
                return;
            }

            $table->dropUnique(['cart_id', 'product_seller_id']);
            $table->dropForeign(['product_seller_id']);
            $table->renameColumn('product_seller_id', 'product_vendor_id');
            $table->foreign('product_vendor_id')
                ->references('id')
                ->on('product_vendors')
                ->cascadeOnDelete();
            $table->unique(['cart_id', 'product_vendor_id']);
        });
    }
};
