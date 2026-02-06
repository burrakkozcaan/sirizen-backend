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
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type'); // privacy_policy, terms_of_service, marketing, data_processing, cookies
            $table->string('consent_version'); // version of the document user agreed to
            $table->boolean('is_granted')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'consent_type']);
            $table->index('consent_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
