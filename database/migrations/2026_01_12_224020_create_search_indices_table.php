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
        Schema::create('search_indices', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->text('searchable_text');
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_indices');
    }
};
