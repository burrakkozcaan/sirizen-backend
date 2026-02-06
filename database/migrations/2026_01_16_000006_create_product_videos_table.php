<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('url');
            $table->string('video_type')->default('youtube');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'video_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_videos');
    }
};
