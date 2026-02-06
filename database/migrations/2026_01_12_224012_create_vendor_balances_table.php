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
        Schema::create('vendor_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('pending_balance', 10, 2)->default(0.00);
            $table->timestamp('last_settlement_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_balances');
    }
};
