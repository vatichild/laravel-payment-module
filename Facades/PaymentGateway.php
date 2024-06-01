<?php
namespace Modules\Payment\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentGateway extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Modules\Payment\Contracts\PaymentGatewayInterface::class;
    }
}