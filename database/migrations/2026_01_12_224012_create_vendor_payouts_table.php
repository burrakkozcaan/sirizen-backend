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
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payout_method');
            $table->string('status');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['vendor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payouts');
    }
};
