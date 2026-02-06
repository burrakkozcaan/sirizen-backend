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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('payment_provider')->nullable()->after('payment_reference');
            $table->string('payment_status')->nullable()->after('payment_provider');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_provider', 'payment_status', 'paid_at']);
        });
    }
};
