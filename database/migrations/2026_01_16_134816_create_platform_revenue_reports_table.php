<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_revenue_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('period_type'); // daily, weekly, monthly, yearly
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('vendor_payouts', 15, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('total_vendors')->default(0);
            $table->unsignedInteger('active_vendors')->default(0);
            $table->unsignedInteger('new_vendors')->default(0);
            $table->unsignedInteger('total_customers')->default(0);
            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('total_products')->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->json('top_categories')->nullable();
            $table->json('top_vendors')->nullable();
            $table->timestamps();

            $table->unique(['report_date', 'period_type']);
            $table->index(['period_type', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_revenue_reports');
    }
};
