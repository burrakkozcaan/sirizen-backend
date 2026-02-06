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
            // Callback alanları
            if (! Schema::hasColumn('payments', 'checkout_token')) {
                $table->string('checkout_token')->nullable()->index()->after('transaction_id');
            }
            if (! Schema::hasColumn('payments', 'callback_status')) {
                $table->string('callback_status')->nullable()->after('checkout_token');
            }
            if (! Schema::hasColumn('payments', 'callback_received_at')) {
                $table->timestamp('callback_received_at')->nullable()->after('callback_status');
            }

            // İade alanları
            if (! Schema::hasColumn('payments', 'refund_id')) {
                $table->string('refund_id')->nullable()->after('split_status');
            }
            if (! Schema::hasColumn('payments', 'refunded_amount')) {
                $table->decimal('refunded_amount', 12, 2)->nullable()->after('refund_id');
            }
            if (! Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refunded_amount');
            }

            // Taksit
            if (! Schema::hasColumn('payments', 'installment_count')) {
                $table->unsignedTinyInteger('installment_count')->nullable()->after('method');
            }

            // Currency
            if (! Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->default('TRY')->after('amount');
            }

            // Payment method
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('method');
            }

            // Metadata
            if (! Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable()->after('error_message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'checkout_token',
                'callback_status',
                'callback_received_at',
                'refund_id',
                'refunded_amount',
                'refunded_at',
                'installment_count',
                'currency',
                'payment_method',
                'metadata',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
