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
        Schema::create('vendor_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            // Sales metrics
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->unsignedInteger('units_sold')->default(0);

            // Commission & Earnings
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('net_earnings', 15, 2)->default(0);
            $table->decimal('pending_payout', 15, 2)->default(0);

            // Product metrics
            $table->unsignedInteger('active_products')->default(0);
            $table->unsignedInteger('out_of_stock_products')->default(0);
            $table->unsignedInteger('products_views')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);

            // Customer metrics
            $table->unsignedInteger('unique_customers')->default(0);
            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('returning_customers')->default(0);

            // Performance metrics
            $table->unsignedInteger('total_reviews')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('questions_answered')->default(0);
            $table->decimal('response_time_hours', 10, 2)->default(0);

            // Order fulfillment
            $table->unsignedInteger('shipped_on_time')->default(0);
            $table->unsignedInteger('late_shipments')->default(0);
            $table->unsignedInteger('cancelled_orders')->default(0);
            $table->unsignedInteger('returned_orders')->default(0);

            $table->timestamps();

            $table->unique(['vendor_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_analytics');
    }
};
