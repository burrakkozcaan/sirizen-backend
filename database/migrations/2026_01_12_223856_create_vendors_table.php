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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('vendor_tiers')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('followers')->default(0);
            $table->unsignedInteger('response_time_avg')->default(0); // in minutes
            $table->decimal('cancel_rate', 5, 2)->default(0.00);
            $table->decimal('return_rate', 5, 2)->default(0.00);
            $table->decimal('late_shipment_rate', 5, 2)->default(0.00);
            $table->string('status')->default('pending'); // active, suspended, pending
            $table->timestamps();

            $table->index(['status', 'rating']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
