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
        Schema::table('vendor_balances', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_balances', 'available_balance')) {
                $table->decimal('available_balance', 12, 2)->default(0.00)->after('balance');
            }
            if (! Schema::hasColumn('vendor_balances', 'total_earnings')) {
                $table->decimal('total_earnings', 12, 2)->default(0.00)->after('pending_balance');
            }
            if (! Schema::hasColumn('vendor_balances', 'total_withdrawn')) {
                $table->decimal('total_withdrawn', 12, 2)->default(0.00)->after('total_earnings');
            }
            if (! Schema::hasColumn('vendor_balances', 'currency')) {
                $table->string('currency', 3)->default('TRY')->after('total_withdrawn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_balances', function (Blueprint $table) {
            $columns = ['available_balance', 'total_earnings', 'total_withdrawn', 'currency'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('vendor_balances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
