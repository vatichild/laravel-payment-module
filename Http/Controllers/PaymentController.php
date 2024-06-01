<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Payment\Models\Payment;
use App\Http\Controllers\Controller;
use Modules\Payment\Facades\PaymentGateway;

class PaymentController extends Controller
{
    /**
     * TODO: make fee + amount calculation to calculate total amount
     */
    public function initiate(Request $request, Payment $payment)
    {
        $provider_fee = 0.29;
        $platform_fee = 0.5;
        //$discount = 0.1;
        $request->merge(['source' => 'REF'.Str::uuid()]);
        $request->merge(['payment_amount' => $request->amount]);
        $request->merge(['total_amount' => $request->amount + $provider_fee + $platform_fee]);
         // TODO: make fee + amount calculation to calculate total amount
        
        $response = PaymentGateway::initiateTransaction($request);

        // Handle errors
        if ($response['status'] === 'error') {
            return response()->json($response, 422);
        }

        $payment->create([
            'payment_amount' => $request->payment_amount, 
            'total_amount' => $request->total_amount, 
            'provider_fee' => $provider_fee, 
            'platform_fee' => $platform_fee, 
            'currency' => $request->currency, 
            'status' => 'initialized',
            'provider_transaction_id' => $response['data']['transactionId'], 
        ]);

        return $response;
    }

    public function charge(Request $request)
    {
        $result = PaymentGateway::charge($request);

        return response()->json($result);
    }

    public function success(Request $request)
    {
        Payment::where('provider_transaction_id', PaymentGateway::transactionId($request))
                 ->update(['status' =>'success', 'data' => $request->all()]);
        return redirect()->to('/payment/success');
    }
    public function cancel(Request $request)
    {
        Payment::where('provider_transaction_id', PaymentGateway::transactionId($request))
                 ->update(['status' =>'cancel', 'data' => $request->all()]);
        return redirect()->to('/payment/cancel');
    }
    public function error(Request $request)
    {
        Payment::where('provider_transaction_id', PaymentGateway::transactionId($request))
                 ->update(['status' =>'error', 'data' => $request->all()]);
        return redirect()->to('/payment/error');
    }
}