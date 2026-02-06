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
        Schema::create('crisp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('crisp_conversations')->cascadeOnDelete();
            $table->string('from'); // user / agent
            $table->text('content');
            $table->string('type')->default('text'); // text, file, etc.
            $table->timestamp('timestamp');
            $table->json('metadata')->nullable(); // Ek bilgiler için
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('timestamp');
            $table->index('from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crisp_messages');
    }
};
