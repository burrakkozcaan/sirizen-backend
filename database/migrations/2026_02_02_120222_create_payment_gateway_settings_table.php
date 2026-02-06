<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique();
            $table->string('display_name');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->json('credentials')->nullable();
            $table->json('configuration')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Varsayılan gateway ayarlarını ekle
        DB::table('payment_gateway_settings')->insert([
            [
                'provider' => 'test',
                'display_name' => 'Test Gateway',
                'is_active' => true,
                'is_test_mode' => true,
                'credentials' => json_encode([]),
                'configuration' => json_encode(['auto_approve' => true]),
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'provider' => 'paytr',
                'display_name' => 'PayTR',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => json_encode([
                    'merchant_id' => '',
                    'merchant_key' => '',
                    'merchant_salt' => '',
                ]),
                'configuration' => json_encode([
                    'max_installment' => 12,
                    'no_installment' => false,
                ]),
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'provider' => 'iyzico',
                'display_name' => 'iyzico',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => json_encode([
                    'api_key' => '',
                    'secret_key' => '',
                ]),
                'configuration' => json_encode([
                    'locale' => 'tr',
                    'currency' => 'TRY',
                ]),
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
};
