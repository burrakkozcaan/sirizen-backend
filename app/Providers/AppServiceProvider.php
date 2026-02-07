<?php

namespace App\Providers;

use App\Models\Vendor;
use App\Observers\VendorObserver;
use App\Services\Cargo\CargoProviderFactory;
use App\Services\Cargo\CargoService;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Payment Gateway Factory
        $this->app->singleton(PaymentGatewayFactory::class, function () {
            return new PaymentGatewayFactory;
        });

        // Payment Service
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(PaymentGatewayFactory::class));
        });

        // Cargo Provider Factory
        $this->app->singleton(CargoProviderFactory::class, function () {
            return new CargoProviderFactory;
        });

        // Cargo Service
        $this->app->singleton(CargoService::class, function ($app) {
            return new CargoService($app->make(CargoProviderFactory::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (config('livewire.temporary_file_upload.disk') === null) {
            config(['livewire.temporary_file_upload.disk' => 'local']);
        }

        // Register Vendor Observer
        Vendor::observe(VendorObserver::class);
    }
}
