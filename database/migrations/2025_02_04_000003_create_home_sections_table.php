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
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('position')->default(0);
            $table->string('section_type'); // vendor_collection_grid, campaign_banner, category_highlight, product_carousel
            $table->json('config_json'); // Dynamic configuration for each section type
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable(); // For scheduled visibility
            $table->timestamp('end_date')->nullable();
            $table->json('visible_for')->nullable(); // Personalization: ["female", "fashion_interest"]
            $table->timestamps();

            $table->index(['is_active', 'position']);
            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
