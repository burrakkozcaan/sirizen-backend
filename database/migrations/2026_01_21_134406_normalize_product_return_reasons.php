<?php

use App\ReturnReason;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('product_returns')
            ->where('reason', 'size_issue')
            ->update(['reason' => ReturnReason::WRONG_SIZE->value]);

        DB::table('product_returns')
            ->where('reason', 'wrong_item')
            ->update(['reason' => ReturnReason::WRONG_PRODUCT->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_returns')
            ->where('reason', ReturnReason::WRONG_SIZE->value)
            ->update(['reason' => 'size_issue']);

        DB::table('product_returns')
            ->where('reason', ReturnReason::WRONG_PRODUCT->value)
            ->update(['reason' => 'wrong_item']);
    }
};
