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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('invoice_type'); // e-invoice, e-archive, proforma
            $table->string('invoice_scenario')->default('basic'); // basic, commercial, export
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('TRY');
            $table->string('status')->default('draft'); // draft, sent, delivered, failed, cancelled
            $table->string('uuid')->nullable()->unique(); // GİB UUID
            $table->text('ettn')->nullable(); // E-Fatura ETTN
            $table->json('invoice_data')->nullable(); // Full invoice XML/JSON data
            $table->json('receiver_info')->nullable(); // Customer/Company info
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('invoice_type');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
