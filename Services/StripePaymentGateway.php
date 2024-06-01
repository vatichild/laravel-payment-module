<?php
namespace Modules\Payment\Services;

use Modules\Payment\Contracts\PaymentGatewayInterface;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function initiateTransaction($request)
    {
        // Stripe init logic here...
    }

    public function charge($request)
    {
        // Stripe charge logic here...
    }
}