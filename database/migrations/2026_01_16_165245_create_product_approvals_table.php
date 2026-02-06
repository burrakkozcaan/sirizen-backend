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
        Schema::create('product_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected, needs_changes
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('changes_requested')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_approvals');
    }
};
