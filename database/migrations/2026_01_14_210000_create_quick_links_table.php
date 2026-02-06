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
        Schema::create('quick_links', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g. price_drops, super_deals
            $table->string('label');         // e.g. "Fiyatı Düşenler"
            $table->string('icon')->nullable(); // Frontend icon name (e.g. TrendingDown)
            $table->string('path');          // Frontend path (e.g. /campaign/price-drops)
            $table->string('color')->nullable(); // Tailwind color class (optional)
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_links');
    }
};
