<?php

namespace Modules\Payment\Facades;

use Illuminate\Support\Facades\Facade;
use \Modules\Payment\Contracts\PaymentGatewayInterface;

class PaymentGateway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PaymentGatewayInterface::class;
    }
}
