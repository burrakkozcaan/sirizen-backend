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
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->foreignId('wishlist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->unique(['wishlist_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropUnique(['wishlist_id', 'product_id']);
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('wishlist_id');
        });
    }
};
