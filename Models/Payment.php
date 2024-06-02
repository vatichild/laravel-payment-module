<?php
namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    protected $fillable = [
        'payout_id',
        'user_id',
        'recipient_id',
        'recurring_payment_id',
        'provider_transaction_id',
        'payment_amount',
        'actual_provider_fee',
        'estimated_provider_fee',
        'actual_platform_fee',
        'estimated_platform_fee',
        'discount_amount',
        'total_amount',
        'currency',
        'reference',
        'promo_code_id',
        'business_account_id',
        'status',
    ];
    
}