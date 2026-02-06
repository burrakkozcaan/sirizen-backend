<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SettleVendorCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public Payment $payment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PaymentService $paymentService): void
    {
        Log::info('Settling vendor commission', [
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
        ]);

        $paymentService->settleCommission($this->order, $this->payment);

        Log::info('Vendor commission settled successfully', [
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SettleVendorCommissionJob failed', [
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
