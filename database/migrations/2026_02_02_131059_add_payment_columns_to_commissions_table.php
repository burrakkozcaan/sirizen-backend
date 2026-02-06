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
        Schema::table('commissions', function (Blueprint $table) {
            if (! Schema::hasColumn('commissions', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('commissions', 'gross_amount')) {
                $table->decimal('gross_amount', 12, 2)->nullable()->after('order_item_id');
            }
            if (! Schema::hasColumn('commissions', 'currency')) {
                $table->string('currency', 3)->default('TRY')->after('net_amount');
            }
            if (! Schema::hasColumn('commissions', 'refunded_amount')) {
                $table->decimal('refunded_amount', 12, 2)->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'payment_id')) {
                $table->dropForeign(['payment_id']);
                $table->dropColumn('payment_id');
            }
            $columns = ['gross_amount', 'currency', 'refunded_amount'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('commissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
