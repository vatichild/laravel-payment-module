<?php
namespace Modules\Payment\Repositories;

use Illuminate\Support\Str;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\PaymentMethod;
use Modules\Payment\Facades\PaymentGateway;
use Modules\Payment\Contracts\PaymentGatewayInterface;

class PaymentRepository {
    
    /**
     * TODO: make fee + amount calculation to calculate total amount
     */
    public function initiate($request)
    {
        $this->prepareForPayment($request);

        
        $initiateResponse = PaymentGateway::initiateTransaction($request);

        // Handle errors
        if ($initiateResponse['status'] === 'error') {
            return response()->json($initiateResponse, 422);
        }

        $this->createPayment($request, $initiateResponse);

        return $initiateResponse;
    }

    public function charge($request, $user_id)
    {
        $this->prepareForPayment($request);
        $paymentMethod = PaymentMethod::select('payment_method_id','exp_month','exp_year')->where('user_id', $user_id)->defaultPayment()->first();
        $data = $request->merge(PaymentGateway::unnormalizeData($paymentMethod));
        $chargeResponse = PaymentGateway::charge($data);
        $this->createPayment($request, $chargeResponse);
        return $chargeResponse;
    }

    /**
     * TODO: modify later user id to real users
     */
    public function handleWebhook($request, $status)
    {
        if(in_array($status,['success','cancel','error'])){
            Payment::where('provider_transaction_id', PaymentGateway::transactionId($request))
            ->update(['status' => $status]);
            if($status =='success')
            {
               $this->handleSuccess($request);
            }
            return redirect()->to('/payment-'.$status);
        }
        return abort(404);
    }


    /**
     * TODO: Update user_id to real users
     */
    public function createPayment($request, $initiateResponse)
    {
        Payment::create([
            'user_id' => 1,
            'payment_amount' => $request->payment_amount,
            'total_amount' => $request->total_amount,
            'estimated_provider_fee' => $request->estimated_provider_fee,
            'estimated_platform_fee' =>  $request->estimated_platform_fee,
            'currency' => $request->currency,
            'reference' => "Private payment",
            'ref_no' => $request->ref_no,
            'status' => 'initialized',
            'provider_transaction_id' => $initiateResponse['data']['transactionId'],
        ]);
    }

    /**
     * TODO: modify later user id to real users
     */
    protected function handleSuccess($request)
    {
        $data = PaymentGateway::checkTransaction($request);
        if ($data['status'] === app()->make(PaymentGatewayInterface::class)::SETTLED) {
            $default = PaymentMethod::where('user_id', 1)->count() ? false : true;
            if (!PaymentMethod::where('user_id', 1)->where('last', PaymentGateway::getLastDigits($data))->count()) {
                PaymentMethod::create(PaymentGateway::normalizeData($data, 1, $default));
            }
        }
    }


    
    protected function prepareForPayment($request)
    {
        // TODO: make fee + amount calculation to calculate total amount
        $provider_fee = 0.29;
        $platform_fee = 0.5;
        //$discount = 0.1;
        $request->merge(['ref_no' => 'REF'.Str::uuid()]);
        $request->merge(['payment_amount' => $request->amount]);
        $request->merge(['estimated_provider_fee' => $provider_fee]);
        $request->merge(['estimated_platform_fee' => $platform_fee]);
        $request->merge(['total_amount' => $request->amount + $request->estimated_provider_fee + $request->estimated_platform_fee]);
    }
    
}