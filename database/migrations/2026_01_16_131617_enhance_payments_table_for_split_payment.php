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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('commission_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('vendor_amount', 10, 2)->default(0)->after('commission_amount');
            $table->decimal('platform_amount', 10, 2)->default(0)->after('vendor_amount');
            $table->string('split_status')->default('pending')->after('status'); // pending, processing, completed, failed
            $table->string('gateway')->nullable()->after('payment_provider'); // iyzico, stripe, paytr
            $table->string('method')->nullable()->after('gateway'); // credit_card, debit_card, bank_transfer, wallet
            $table->json('gateway_response')->nullable()->after('transaction_id');
            $table->text('error_message')->nullable()->after('gateway_response');
            $table->unsignedInteger('installment')->default(1)->after('method');

            $table->index('split_status');
            $table->index('gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'commission_amount',
                'vendor_amount',
                'platform_amount',
                'split_status',
                'gateway',
                'method',
                'gateway_response',
                'error_message',
                'installment',
            ]);
        });
    }
};
