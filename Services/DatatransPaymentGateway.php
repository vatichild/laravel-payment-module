<?php
namespace Modules\Payment\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\Payment\Contracts\PaymentGatewayInterface;

class DatatransPaymentGateway implements PaymentGatewayInterface
{
    const SETTLED = 'settled';
    const CANCELED = 'canceled';
    const DECLINED = 'declined';

    public function initiateTransaction($request)
    {
        // Validate the request
        $validator = $this->validateRequest($request);

        if($validator->fails()) {
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

    public function checkTransaction($request)
    {
        $response = Http::datatrans()->get('transactions/'.self::transactionId($request));
        $checkedResponse = $this->checkResponse($response);
        if($checkedResponse['status'] ==='success'){
            return $checkedResponse['data'];
        }
        return $checkedResponse;
    }

    public function charge($payload)
    {
        $data = $payload->only(['currency','refno','card','autoSettle']);
        $data['amount'] = $payload->total_amount * 100;
        $response = Http::datatrans()->post('transactions/authorize', $data);
        return $this->checkResponse($response);
    }

    public function transactionId($request)
    {
        return $request->input('datatransTrxId');
    }


    public function normalizeData($data, $user_id, $default = false){
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

    public function unnormalizeData($data)
    {
        return ['card' => [
            'alias' => $data->payment_method_id,
            'expiryYear' => $data->exp_year,
            'expiryMonth' => $data->exp_month,
        ]];
    }

    public function getLastDigits($data)
    {
        return Str::substr($data['card']['masked'], -4);
    }


    private function validateRequest($request)
    {
        return Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string']
        ]);
    }

    /**
     * Check the response from an HTTP request and return the status and data or errors.
     *
     * @param \Illuminate\Http\Client\Response $response The HTTP response object.
     * @return array An array containing the status and data or errors.
     */
    private function checkResponse($response): array
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
    
        return [
            'status' => 'success',
            'data' => $response->json()
        ];
    }
}