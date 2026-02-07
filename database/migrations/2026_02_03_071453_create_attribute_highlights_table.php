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
        Schema::create('attribute_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('display_label')->nullable(); // Özel gösterim adı (örn: "Pamuklu" yerine "100% Pamuk")
            $table->string('icon')->nullable(); // Özel ikon
            $table->string('color')->nullable(); // vurgu rengi
            $table->integer('priority')->default(0); // Gösterim sırası
            $table->boolean('show_in_pdp')->default(true); // PDP'de göster
            $table->boolean('show_in_list')->default(false); // Liste görünümünde göster
            $table->timestamps();

            $table->unique(['attribute_id', 'category_group_id']);
            $table->index(['category_group_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_highlights');
    }
};
