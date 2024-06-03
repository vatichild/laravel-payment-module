<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

Route::group(['prefix' => 'payment'], function(){
    Route::get('success', [PaymentController::class, 'success']);

    Route::get('cancel', [PaymentController::class, 'cancel']);
    
    Route::get('error', [PaymentController::class, 'error']);
});

