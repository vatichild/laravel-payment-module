<?php
namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    protected $fillable = [
        'provider_transaction_id',
        'payment_amount',
        'provider_fee',
        'platform_fee',
        'total_amount',
        'currency',
        'source',
        'status',
        'data'
    ];
    
}