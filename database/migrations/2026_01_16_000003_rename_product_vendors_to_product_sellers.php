<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_vendors')) {
            Schema::rename('product_vendors', 'product_sellers');
        }

        if (!Schema::hasTable('product_sellers')) {
            Schema::create('product_sellers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
                $table->decimal('price', 10, 2);
                $table->unsignedInteger('stock')->default(0);
                $table->unsignedInteger('dispatch_days')->default(3);
                $table->enum('shipping_type', ['normal', 'express', 'same_day'])->default('normal');
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
                $table->unique(['product_id', 'vendor_id']);
                $table->index(['vendor_id', 'stock']);
            });
        } else {
            Schema::table('product_sellers', function (Blueprint $table) {
                if (Schema::hasColumn('product_sellers', 'shipping_time')) {
                    $table->renameColumn('shipping_time', 'dispatch_days');
                } else {
                    $table->unsignedInteger('dispatch_days')->default(3)->after('stock');
                }

                if (!Schema::hasColumn('product_sellers', 'shipping_type')) {
                    $table->enum('shipping_type', ['normal', 'express', 'same_day'])->default('normal')->after('dispatch_days');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_sellers')) {
            Schema::table('product_sellers', function (Blueprint $table) {
                if (Schema::hasColumn('product_sellers', 'dispatch_days')) {
                    $table->renameColumn('dispatch_days', 'shipping_time');
                }

                if (Schema::hasColumn('product_sellers', 'shipping_type')) {
                    $table->dropColumn('shipping_type');
                }
            });

            Schema::rename('product_sellers', 'product_vendors');
        }
    }
};
