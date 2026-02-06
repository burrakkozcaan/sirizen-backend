<?php

declare(strict_types=1);

namespace App\Jobs;

use App\PaymentProvider;
use App\Services\Payment\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 300];

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public PaymentProvider $provider,
        public array $payload
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PaymentService $paymentService): void
    {
        Log::info('Processing payment callback', [
            'provider' => $this->provider->value,
            'payload' => $this->payload,
        ]);

        $result = $paymentService->handleCallback($this->provider, $this->payload);

        if (! $result['success']) {
            Log::warning('Payment callback processing failed', [
                'provider' => $this->provider->value,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
        } else {
            Log::info('Payment callback processed successfully', [
                'provider' => $this->provider->value,
                'payment_id' => $result['payment']?->id ?? null,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessPaymentCallbackJob failed', [
            'provider' => $this->provider->value,
            'payload' => $this->payload,
            'error' => $exception->getMessage(),
        ]);
    }
}
