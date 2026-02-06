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
        Schema::create('vendor_sla_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->date('metric_date');
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('cancelled_orders')->default(0);
            $table->unsignedInteger('returned_orders')->default(0);
            $table->unsignedInteger('late_shipments')->default(0);
            $table->unsignedInteger('on_time_shipments')->default(0);
            $table->decimal('cancel_rate', 5, 2)->default(0.00); // percentage
            $table->decimal('return_rate', 5, 2)->default(0.00); // percentage
            $table->decimal('late_shipment_rate', 5, 2)->default(0.00); // percentage
            $table->unsignedInteger('avg_shipment_time')->default(0); // hours
            $table->unsignedInteger('avg_response_time')->default(0); // minutes
            $table->unsignedInteger('total_questions_answered')->default(0);
            $table->unsignedInteger('total_reviews_responded')->default(0);
            $table->decimal('customer_satisfaction_score', 3, 2)->default(0.00); // 0-5
            $table->json('sla_violations')->nullable(); // array of violated metrics
            $table->timestamps();

            $table->unique(['vendor_id', 'metric_date']);
            $table->index('metric_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_sla_metrics');
    }
};
