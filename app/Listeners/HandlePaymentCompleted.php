<?php

declare(strict_types=1);

namespace App\Listeners;

use App\CommissionStatus;
use App\Events\PaymentCompleted;
use App\Models\Commission;
use App\Models\VendorBalance;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandlePaymentCompleted implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        DB::transaction(function () use ($payment) {
            // Order'ı paid yap
            $payment->order()->update([
                'payment_status' => 'paid',
                'payment_reference' => $payment->transaction_id,
            ]);

            // Henüz işlenmemiş (PENDING) komisyonları güncelle
            // (settleCommission zaten PAID yapmışsa bu adım atlanır)
            $commissions = Commission::whereHas('orderItem', function ($q) use ($payment) {
                $q->where('order_id', $payment->order_id);
            })->where('status', CommissionStatus::PENDING)->with('vendor')->get();

            foreach ($commissions as $commission) {
                // Komisyon payment_id set et ve durumu güncelle
                $commission->update([
                    'payment_id' => $payment->id,
                    'status'     => CommissionStatus::PAID,
                ]);

                // Vendor bakiyesini güncelle (pending_balance artır)
                $balance = VendorBalance::firstOrCreate(
                    ['vendor_id' => $commission->vendor_id],
                    [
                        'balance'           => 0,
                        'available_balance' => 0,
                        'pending_balance'   => 0,
                        'total_earnings'    => 0,
                        'total_withdrawn'   => 0,
                        'currency'          => 'TRY',
                    ]
                );

                $balance->increment('pending_balance', $commission->net_amount);
                $balance->increment('balance', $commission->net_amount);
                $balance->increment('total_earnings', $commission->net_amount);
            }

            Log::info('PaymentCompleted handled', [
                'payment_id'   => $payment->id,
                'order_id'     => $payment->order_id,
                'commissions'  => $commissions->count(),
            ]);
        });
    }

    public function failed(PaymentCompleted $event, \Throwable $exception): void
    {
        Log::error('HandlePaymentCompleted failed', [
            'payment_id' => $event->payment->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
