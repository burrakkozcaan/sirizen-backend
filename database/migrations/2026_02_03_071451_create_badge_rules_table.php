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
        Schema::create('badge_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_definition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_group_id')->nullable()->constrained()->nullOnDelete(); // null = tüm kategoriler
            $table->string('condition_type'); // price_discount, review_count, stock, fast_delivery, is_new, is_bestseller, custom
            $table->json('condition_config'); // {"operator": ">=", "value": 30, "field": "discount_percent"}
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['badge_definition_id', 'category_group_id', 'is_active']);
            $table->index('condition_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_rules');
    }
};
