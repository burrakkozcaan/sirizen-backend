<?php

declare(strict_types=1);

namespace App\Listeners;

use App\CommissionStatus;
use App\Events\PaymentFailed;
use App\Models\Commission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandlePaymentFailed implements ShouldQueue
{
    public function handle(PaymentFailed $event): void
    {
        $payment = $event->payment;

        // Order durumunu güncelle
        $payment->order()->update([
            'payment_status' => 'failed',
        ]);

        // Bekleyen komisyonları iptal et
        Commission::whereHas('orderItem', function ($q) use ($payment) {
            $q->where('order_id', $payment->order_id);
        })->where('status', CommissionStatus::PENDING->value)
          ->update(['status' => CommissionStatus::CANCELLED->value]);

        Log::warning('PaymentFailed handled', [
            'payment_id' => $payment->id,
            'order_id'   => $payment->order_id,
            'reason'     => $event->reason,
        ]);
    }

    public function failed(PaymentFailed $event, \Throwable $exception): void
    {
        Log::error('HandlePaymentFailed listener failed', [
            'payment_id' => $event->payment->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
