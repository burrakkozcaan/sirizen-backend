<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_modal_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('layout_config'); // Block dizilimi
            $table->json('rules')->nullable(); // Ek kurallar (örn: stok < 5 uyarı)
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_group_id', 'is_active', 'is_default']);
        });

        Schema::create('cart_modal_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // variant_size, variant_color, seller_selector
            $table->string('name');
            $table->string('component'); // React component adı
            $table->enum('type', ['static', 'dynamic', 'conditional'])->default('static');
            $table->json('default_props')->nullable();
            $table->json('validation_rules')->nullable(); // Form validasyonu
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_modal_blocks');
        Schema::dropIfExists('cart_modal_layouts');
    }
};
