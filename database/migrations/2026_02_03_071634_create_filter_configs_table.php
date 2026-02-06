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
        Schema::create('filter_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_group_id')->constrained()->cascadeOnDelete();
            $table->string('filter_type'); // attribute, price, brand, rating, seller, campaign
            $table->foreignId('attribute_id')->nullable()->constrained()->nullOnDelete(); // attribute filter için
            $table->string('display_label'); // Görünen ad
            $table->string('filter_component')->default('checkbox'); // checkbox, range, select, multiselect
            $table->integer('order')->default(0);
            $table->boolean('is_collapsed')->default(false); // Varsayılan olarak daraltılmış mı?
            $table->boolean('show_count')->default(true); // Seçenek sayısı gösterilsin mi?
            $table->json('config')->nullable(); // {"min": 0, "max": 10000, "step": 100}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_group_id', 'filter_type', 'is_active']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_configs');
    }
};
