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
        Schema::create('attribute_sets', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // dis_giyim_erkek, telefon_aksesuar
            $table->string('name'); // Dış Giyim - Erkek
            $table->foreignId('category_group_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['key', 'is_active']);
        });

        // Kategori - Attribute Set ilişkisi
        Schema::create('category_attribute_set', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_set_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['category_id', 'attribute_set_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_attribute_set');
        Schema::dropIfExists('attribute_sets');
    }
};
