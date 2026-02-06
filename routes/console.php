<?php

use App\Jobs\RecalculateBuyBoxJob;
use App\Jobs\SyncShipmentTrackingJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new RecalculateBuyBoxJob)
    ->dailyAt('03:00')
    ->name('buybox-recalculate')
    ->withoutOverlapping()
    ->onOneServer();

// Kargo takip senkronizasyonu - her 30 dakikada bir
Schedule::job(new SyncShipmentTrackingJob)
    ->everyThirtyMinutes()
    ->name('shipment-tracking-sync')
    ->withoutOverlapping()
    ->onOneServer();

// ==========================================
// VENDOR SETTLEMENT & PAYOUT SCHEDULERS
// Trendyol mantığı - haftalık hakediş sistemi
// ==========================================

// Teslim edilen siparişlerin bakiyelerini kesinleştir
// Her gün 04:00'da çalışır - 7 gün geçmiş teslimatları işler
Schedule::command('vendor:settle-delivered --days=7')
    ->dailyAt('04:00')
    ->name('vendor-settle-delivered')
    ->withoutOverlapping()
    ->onOneServer()
    ->emailOutputOnFailure(config('mail.admin_email'));

// Haftalık hakediş hesaplaması
// Her Pazartesi 06:00'da çalışır - geçen haftanın ödemelerini hesaplar
Schedule::command('vendor:process-payouts --min-amount=100')
    ->weeklyOn(1, '06:00') // 1 = Pazartesi
    ->name('vendor-weekly-payouts')
    ->withoutOverlapping()
    ->onOneServer()
    ->emailOutputOnFailure(config('mail.admin_email'));
