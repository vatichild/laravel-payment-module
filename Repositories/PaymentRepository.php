<?php

namespace Modules\Payment\Repositories;

use Illuminate\Support\Str;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\PaymentMethod;
use Modules\Payment\Facades\PaymentGateway;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use \Illuminate\Http\RedirectResponse;
use \Illuminate\Http\JsonResponse;

class PaymentRepository
{

    /**
     * Initiate transaction to get transactionId before proceed to payment
     * @param $request
     * @return JsonResponse|array
     */
    public function initiate($request): JsonResponse|array
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

    /**
     * Charge user with saved card/payment method
     * @param $request
     * @param $user_id
     * @return array
     */
    public function charge($request, $user_id): array
    {
        $this->prepareForPayment($request);
        $paymentMethod = PaymentMethod::select('payment_method_id', 'exp_month', 'exp_year')->where('user_id', $user_id)->defaultPayment()->first();
        $request->merge(PaymentGateway::unnormalizeData($paymentMethod));
        $chargeResponse = PaymentGateway::charge($request);
        $this->createPayment($request, $chargeResponse);
        return $chargeResponse;
    }

    /**
     * TODO: modify later user id to real users
     * Handle webhook success, cancel, error
     * @param $request
     * @param $status
     * @return RedirectResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handleWebhook($request, $status): RedirectResponse
    {
        if (in_array($status, ['success', 'cancel', 'error'])) {
            Payment::where('provider_transaction_id', PaymentGateway::transactionId($request))
                ->update(['status' => $status]);
            if ($status == 'success') {
                $this->handleSuccess($request);
            }
            return redirect()->to('/payment-' . $status);
        }
        return redirect()->to(404);
    }


    /**
     * TODO: Update user_id to real users
     * Create payment
     * @param $request
     * @param $initiateResponse
     * @return void
     */
    public function createPayment($request, $initiateResponse): void
    {
        Payment::create([
            'user_id' => 1,
            'payment_amount' => $request->payment_amount,
            'total_amount' => $request->total_amount,
            'estimated_provider_fee' => $request->estimated_provider_fee,
            'estimated_platform_fee' => $request->estimated_platform_fee,
            'currency' => $request->currency,
            'reference' => "Private payment",
            'ref_no' => $request->ref_no,
            'status' => $request->status,
            'provider_transaction_id' => $initiateResponse['data']['transactionId'],
        ]);
    }

    /**
     * TODO: modify later user id to real users
     * On success payment webhook
     * @param $request
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handleSuccess($request): void
    {
        $data = PaymentGateway::checkTransaction($request);
        if ($data['status'] === app()->make(PaymentGatewayInterface::class)::SETTLED) {
            $default = !PaymentMethod::where('user_id', 1)->count();
            if (!PaymentMethod::where('user_id', 1)->where('last', PaymentGateway::getLastDigits($data))->count()) {
                PaymentMethod::create(PaymentGateway::normalizeData($data, 1, $default));
            }
        }
    }


    /**
     * Prepare request data for payment record
     * @param $request
     * @return void
     */
    protected function prepareForPayment($request): void
    {
        // TODO: make fee + amount calculation to calculate total amount
        $provider_fee = 0.29;
        $platform_fee = 0.5;
        //$discount = 0.1;
        $request->merge(['ref_no' => 'REF' . Str::uuid()]);
        $request->merge(['payment_amount' => $request->amount]);
        $request->merge(['estimated_provider_fee' => $provider_fee]);
        $request->merge(['estimated_platform_fee' => $platform_fee]);
        $request->merge(['total_amount' => $request->amount + $request->estimated_provider_fee + $request->estimated_platform_fee]);
    }

}
