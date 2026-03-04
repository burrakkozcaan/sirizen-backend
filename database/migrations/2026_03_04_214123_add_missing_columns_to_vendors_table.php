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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('name');
            $table->unsignedInteger('product_count')->default(0)->after('total_orders');
            $table->unsignedInteger('review_count')->default(0)->after('product_count');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['logo', 'product_count', 'review_count']);
        });
    }
};
