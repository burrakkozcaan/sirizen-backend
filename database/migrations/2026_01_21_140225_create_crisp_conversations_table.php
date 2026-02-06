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
        Schema::create('crisp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('website_id');
            $table->string('nickname')->nullable();
            $table->string('crisp_user_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('open'); // open, closed
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index('session_id');
            $table->index('website_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crisp_conversations');
    }
};
