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
        Schema::create('data_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('request_type'); // full_deletion, anonymization, data_export
            $table->string('status')->default('pending'); // pending, approved, processing, completed, rejected
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_deletion_requests');
    }
};
