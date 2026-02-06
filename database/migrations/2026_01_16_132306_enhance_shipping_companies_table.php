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
        Schema::table('shipping_companies', function (Blueprint $table) {
            $table->string('webhook_url')->nullable()->after('api_url');
            $table->string('webhook_secret')->nullable()->after('webhook_url');
            $table->json('supported_services')->nullable()->after('api_credentials'); // express, standard, economy
            $table->decimal('base_price', 8, 2)->default(0)->after('supported_services');
            $table->decimal('price_per_kg', 8, 2)->default(0)->after('base_price');
            $table->decimal('price_per_desi', 8, 2)->default(0)->after('price_per_kg');
            $table->unsignedInteger('free_shipping_threshold')->nullable()->after('price_per_desi'); // minimum order amount for free shipping
            $table->json('coverage_areas')->nullable()->after('free_shipping_threshold'); // supported cities/districts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_companies', function (Blueprint $table) {
            $table->dropColumn([
                'webhook_url',
                'webhook_secret',
                'supported_services',
                'base_price',
                'price_per_kg',
                'price_per_desi',
                'free_shipping_threshold',
                'coverage_areas',
            ]);
        });
    }
};
