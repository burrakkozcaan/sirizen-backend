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
        Schema::create('product_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // null = rule-based
            $table->string('block_type'); // banner | badge | shipping | advantage | notice
            $table->string('position'); // above_price | under_price | under_gallery | sidebar
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'position', 'is_active']);
            $table->index(['block_type', 'position', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_blocks');
    }
};
