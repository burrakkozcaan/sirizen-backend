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
        Schema::create('block_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('product_blocks')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // icon key (lucide-react)
            $table->string('image')->nullable();
            $table->string('color')->nullable(); // primary | danger | warning | success | info
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_contents');
    }
};
