<?php

namespace Modules\Payment\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\Facades\PaymentGateway;

class DatatransPaymentGateway implements PaymentGatewayInterface
{
    const SUCCEEDED = 'settled';
    const CANCELED = 'canceled';
    const FAILED = 'failed';

    /**
     * Initiate transaction to get transactionId before proceed to payment
     * @param $request
     * @return array
     */
    public function initiateTransaction($request): array
    {
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors()
            ];
        }

        $data = [
            'currency' => $request->currency,
            'refno' => $request->ref_no,
            'amount' => $request->total_amount * 100,
            ...config('payment.gateways.datatrans.init'),
        ];

        //Make the api call
        $response = Http::datatrans()->post('transactions', $data);

        return $this->checkResponse($response);
    }

    /**
     * Check transaction status and get alias
     * @param $request
     * @param bool $returnData
     * @return array
     */
    public function checkTransaction($request, bool $returnData = false): array
    {
        $response = Http::datatrans()->get('transactions/' . self::transactionId($request));
        $checkedResponse = $this->checkResponse($response);
        if ($returnData && $checkedResponse['status'] === 'success') {
            return $checkedResponse['data'];
        }
        return $checkedResponse;
    }

    /**
     * Charge user from saved card/payment method
     * @param $request
     * @return array
     */
    public function charge($request): array
    {
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors()
            ];
        }

        $data = $request->only(['currency', 'refno', 'card', 'autoSettle']);

        $data['amount'] = $request->total_amount * 100;

        $response = Http::datatrans()->post('transactions/authorize', $data);

        return $this->checkResponse($response, true);
    }

    /**
     * Get transaction id
     * @param $request
     * @return mixed
     */
    public function transactionId($request): string
    {
        return $request->input('transactionId');
    }


    /**
     * Normalize data from datatrans to module db table/model
     * @param $data
     * @param $user_id
     * @param bool $default
     * @return array
     */
    public function normalizeData($data, $user_id, $default = false): array
    {
        $card = $data['card'];
        return [
            'user_id' => $user_id,
            'payment_method_id' => $card['alias'],
            'type' => $card['info']['type'],
            'brand' => $card['info']['brand'],
            'last' => self::getLastDigits($data),
            'exp_month' => $card['expiryMonth'],
            'exp_year' => $card['expiryYear'],
            'default' => $default,
        ];
    }

    /**
     * Make data readable for datatrans endpoint
     * @param $data
     * @return array[]
     */
    public function unnormalizeData($data): array
    {
        return [
            'card' => [
                'alias' => $data->payment_method_id,
                'expiryYear' => $data->exp_year,
                'expiryMonth' => $data->exp_month,
            ]
        ];
    }

    /**
     * Get last 4 digits from masked payment method/card
     * @param $data
     * @return string
     */
    public function getLastDigits($data): string
    {
        return Str::substr($data['card']['masked'], -4);
    }


    /**
     * Validate payment required fields
     * @param $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequest($request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string']
        ]);
    }

    /**
     * Check the response from an HTTP request and return the status and data or errors.
     *
     * @param Response $response
     * @param bool $checkTransaction
     * @return array
     */
    private function checkResponse(Response $response, bool $checkTransaction = false): array
    {
        if ($response->failed()) {
            $error = $response->json()['error'] ?? 'Unknown error';
            $statusCode = $response->status();

            Log::error('API Request failed', ['status_code' => $statusCode, 'error' => $error]);

            return [
                'status' => 'error',
                'errors' => $error
            ];
        }

        if ($checkTransaction) {
            return PaymentGateway::checkTransaction(new \Illuminate\Http\Request($response->json()));
        }

        return [
            'status' => 'success',
            'data' => $response->json()
        ];
    }
}
