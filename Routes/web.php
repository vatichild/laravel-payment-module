<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

Route::group(['prefix' => 'payment'], function(){
    Route::get('{status}', [PaymentController::class, 'handleWebhook']);
});

