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
        Schema::create('social_proof_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // cart_count, view_count, sold_count, review_count
            $table->string('display_format'); // "{count} kişinin sepetinde", "Son 24 saatte {count} görüntüleme"
            $table->string('threshold_type')->default('fixed'); // fixed, percentage
            $table->integer('threshold_value')->default(0); // 1000 (fixed) veya 10 (yüzde)
            $table->integer('refresh_interval')->default(300); // Kaç saniyede bir güncellensin
            $table->string('position')->default('under_title'); // under_title, near_price, under_gallery
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_group_id', 'type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_proof_rules');
    }
};
