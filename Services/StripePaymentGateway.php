<?php

namespace Modules\Payment\Services;

use Modules\Payment\Contracts\PaymentGatewayInterface;

class StripePaymentGateway implements PaymentGatewayInterface
{
    const SUCCEEDED = 'succeeded';
    const CANCELED = 'canceled';
    const FAILED = 'failed';

    public function initiateTransaction($request): array
    {
        // Stripe init logic here...
    }

    public function charge($request): array
    {
        // Stripe charge logic here...
    }

    public function checkTransaction($request): array
    {
        // TODO: Implement checkTransaction() method.
    }

    public function transactionId($request): string
    {
        // TODO: Implement transactionId() method.
    }

    public function normalizeData($data, $user_id, $default): array
    {
        // TODO: Implement normalizeData() method.
    }

    public function unnormalizeData($data): array
    {
        // TODO: Implement unnormalizeData() method.
    }

    public function getLastDigits($data): string
    {
        // TODO: Implement getLastDigits() method.
    }
}
