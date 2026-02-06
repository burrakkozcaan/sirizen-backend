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
        Schema::create('vendor_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // bronze, silver, gold, platinum, elite
            $table->integer('min_total_orders')->default(0);
            $table->decimal('min_rating', 3, 2)->default(0.00);
            $table->decimal('max_cancel_rate', 5, 2)->default(100.00);
            $table->decimal('max_return_rate', 5, 2)->default(100.00);
            $table->integer('priority_boost')->default(0);
            $table->string('badge_icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_tiers');
    }
};
