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
        Schema::create('cargo_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete(); // null means platform-wide integration
            $table->string('integration_type'); // api, manual, webhook
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('customer_code')->nullable(); // vendor's customer code with cargo company
            $table->json('api_credentials')->nullable(); // additional credentials
            $table->json('configuration')->nullable(); // custom settings per vendor
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['shipping_company_id', 'vendor_id']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_integrations');
    }
};
