<?php
namespace Modules\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function initiateTransaction($request);
    public function charge($request);
}