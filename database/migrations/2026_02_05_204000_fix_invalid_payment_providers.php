<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Veritabanındaki geçersiz payment_provider değerlerini güncelle
        // stripe ve param değerlerini test olarak değiştir
        DB::table('payments')
            ->whereIn('payment_provider', ['stripe', 'param'])
            ->update(['payment_provider' => 'test']);

        // orders tablosundaki geçersiz payment_provider değerlerini güncelle
        if (Schema::hasColumn('orders', 'payment_provider')) {
            DB::table('orders')
                ->whereIn('payment_provider', ['stripe', 'param'])
                ->update(['payment_provider' => 'test']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi gerekli değil
    }
};
