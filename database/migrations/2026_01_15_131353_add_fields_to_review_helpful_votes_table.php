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
        Schema::table('review_helpful_votes', function (Blueprint $table) {
            $table->foreignId('product_review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_helpful')->default(true);

            $table->unique(['product_review_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review_helpful_votes', function (Blueprint $table) {
            $table->dropUnique(['product_review_id', 'user_id']);
            $table->dropColumn('is_helpful');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('product_review_id');
        });
    }
};
