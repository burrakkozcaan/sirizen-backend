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
        Schema::create('pdp_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Varsayılan, Kampanyalı, Flash Ürün
            $table->json('layout_config'); // Blok sıralaması ve ayarları
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_group_id', 'is_active']);
        });

        // PDP Blok Tanımları (sistem blokları)
        Schema::create('pdp_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // gallery, title, rating, size_selector, attributes_highlight
            $table->string('name'); // Görsel Galeri, Ürün Başlığı, Boyut Seçici
            $table->string('component'); // React component adı
            $table->string('type')->default('static'); // static, dynamic, conditional
            $table->json('default_props')->nullable(); // Varsayılan ayarlar
            $table->json('allowed_positions')->nullable(); // ["top", "sidebar", "bottom"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['key', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdp_blocks');
        Schema::dropIfExists('pdp_layouts');
    }
};
