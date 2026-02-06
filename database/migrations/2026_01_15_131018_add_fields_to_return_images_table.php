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
        Schema::table('return_images', function (Blueprint $table) {
            $table->foreignId('product_return_id')->constrained('product_returns')->cascadeOnDelete();
            $table->string('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_return_id');
            $table->dropColumn('image');
        });
    }
};
