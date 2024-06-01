<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

Route::post('/initiate', [PaymentController::class, 'initiate']);