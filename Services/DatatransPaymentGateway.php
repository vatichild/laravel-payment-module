<?php
namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\Payment\Contracts\PaymentGatewayInterface;

class DatatransPaymentGateway implements PaymentGatewayInterface
{
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
            'refno' => $request->source,
            'amount' => $request->total_amount * 100,
            ...config('payment.datatrans.init'),
        ];

        //Make the api call
        $response = Http::datatrans()->post('transactions', $data);

        if($response->failed()){
            return [
                'status' => 'error',
                'errors' => $response->json()['error']
            ];
        }

        return ['status'=>'success', 'data' => $response->json()];
    }

    public function charge($payload)
    {
        // Datatrans charge logic here...
    }

    public function transactionId($request)
    {
        return $request->input('datatransTrxId');
    }


    private function validateRequest($request)
    {
        return Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string']
        ]);
    }
}