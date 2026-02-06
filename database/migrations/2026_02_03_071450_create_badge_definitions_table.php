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
        Schema::create('badge_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // fast_delivery, advantage, best_seller, discount
            $table->string('label'); // Varsayılan etiket
            $table->string('icon')->nullable();
            $table->string('color')->default('blue'); // Varsayılan renk
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
            $table->integer('priority')->default(0); // Gösterim önceliği
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['key', 'is_active']);
            $table->index('priority');
        });

        // Kategori grubuna göre badge çevirileri/özelleştirmeleri
        Schema::create('badge_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_definition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('label'); // "Avantajlı Ürün" vs "Kampanyalı Ürün"
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
            $table->timestamps();

            $table->unique(['badge_definition_id', 'category_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_translations');
        Schema::dropIfExists('badge_definitions');
    }
};
