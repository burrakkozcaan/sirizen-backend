<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ürün kaydedilirken hesaplanan badge'lerin önbelleği
     */
    public function up(): void
    {
        Schema::create('product_badge_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_definition_id')->constrained()->cascadeOnDelete();
            $table->string('label'); // Hesaplanmış etiket
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamp('expires_at')->nullable(); // Süreli badge'ler için
            $table->timestamps();

            $table->unique(['product_id', 'badge_definition_id']);
            $table->index(['product_id', 'priority']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_badge_snapshots');
    }
};
