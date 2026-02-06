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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_set_id')->constrained()->cascadeOnDelete();
            $table->string('key'); // beden, renk, materyal, ram, depolama
            $table->string('label'); // Beden, Renk, Materyal
            $table->string('type')->default('select'); // select, text, number, boolean, multiselect
            $table->json('options')->nullable(); // ["S", "M", "L", "XL"] veya null
            $table->string('unit')->nullable(); // GB, cm, kg
            $table->boolean('is_filterable')->default(false); // Filtrede gösterilsin mi?
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['attribute_set_id', 'is_active']);
            $table->index(['is_filterable', 'is_active']);
        });

        // Ürün - Attribute değerleri (mevcut product_attributes yerine normalize edilmiş)
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value'); // "M", "Kırmızı", "16GB"
            $table->timestamps();

            $table->unique(['product_id', 'attribute_id']);
            $table->index(['attribute_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('attributes');
    }
};
