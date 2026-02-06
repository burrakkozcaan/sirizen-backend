<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode', 50)
                ->nullable()
                ->unique()
                ->after('slug');

            $table->string('model_code', 50)
                ->nullable()
                ->after('barcode');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('barcode', 50)
                ->nullable()
                ->unique()
                ->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'model_code']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
