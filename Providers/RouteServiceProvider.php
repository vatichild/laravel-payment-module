<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;


class RouteServiceProvider extends BaseRouteServiceProvider
{


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->routes(function () {
           Route::middleware('api')->prefix('api/payment')
                ->group(__DIR__. '/../Routes/api.php');
           Route::middleware('web')
                ->group(__DIR__. '/../Routes/web.php');
        });
    }
}
