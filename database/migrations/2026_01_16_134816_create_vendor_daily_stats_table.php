<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->date('stat_date');
            $table->unsignedInteger('total_sales')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('net_revenue', 12, 2)->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->unsignedInteger('products_sold')->default(0);
            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('returning_customers')->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('product_views')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['vendor_id', 'stat_date']);
            $table->index('stat_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_daily_stats');
    }
};
