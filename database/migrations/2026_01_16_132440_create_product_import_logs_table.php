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
        Schema::create('product_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // csv, xlsx, xml, json
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->json('errors')->nullable(); // array of errors per row
            $table->text('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_import_logs');
    }
};
