<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Services\StripePaymentGateway;
use Modules\Payment\Providers\RouteServiceProvider;
use Modules\Payment\Services\DatatransPaymentGateway;
use Modules\Payment\Contracts\PaymentGatewayInterface;


class PaymentServiceProvider extends ServiceProvider
{
    protected $config;
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            switch (config('payment.gateway')) {
                case 'stripe':
                    return new StripePaymentGateway();
                default:
                    return new DatatransPaymentGateway();
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__. '/../config.php', 'payment');
        
        $this->app->register(RouteServiceProvider::class);

        $this->defineMacros();


    }

    protected function gatewayConfig($key)
    {
       return config('payment.'. config('payment.gateway') .'.'. $key);
    }

    private function defineMacros(): void
    {
        $gateway = config('payment.gateway');

        Http::macro($gateway, function () use ($gateway){
            return  Http::withHeaders(config('payment.'.$gateway.'.headers'))->baseUrl(config('payment.'.$gateway.'.url'));
        });
    }
}
