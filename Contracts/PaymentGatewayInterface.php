<?php

namespace Modules\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function initiateTransaction($request): array;

    public function charge($request): array;

    public function checkTransaction($request): array;

    public function transactionId($request): string;

    public function normalizeData($data, $user_id, $default): array;

    public function unnormalizeData($data): array;

    public function getLastDigits($data): string;
}
