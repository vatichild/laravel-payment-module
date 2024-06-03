<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Payment\Repositories\PaymentRepository;

class PaymentController extends Controller
{
    protected PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function initiate(Request $request)
    {
        return $this->paymentRepository->initiate($request);
    }

    public function charge(Request $request, $user_id)
    {
        return $this->paymentRepository->charge($request, $user_id);
    }

    /**
     * @throws BindingResolutionException
     */
    public function handleWebhook(Request $request, $status)
    {
        return $this->paymentRepository->handleWebhook($request, $status);
    }
}
