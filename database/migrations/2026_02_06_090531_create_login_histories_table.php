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
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->index(); // IPv6 desteği için 45 karakter
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_suspicious')->default(false); // Şüpheli aktivite
            $table->boolean('is_new_location')->default(false); // Yeni konum
            $table->boolean('is_new_device')->default(false); // Yeni cihaz
            $table->timestamp('logged_in_at');
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'logged_in_at']);
            $table->index(['ip_address', 'logged_in_at']);
            $table->index('is_suspicious');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
