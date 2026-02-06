<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filter_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('filter_key'); // beden, renk, marka
            $table->string('filter_value'); // M, Siyah, Nike
            $table->unsignedInteger('count')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['category_id', 'filter_key', 'filter_value']);
            $table->index(['category_id', 'filter_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filter_counts');
    }
};
