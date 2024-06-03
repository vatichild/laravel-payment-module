<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'type',
        'last',
        'brand',
        'exp_month',
        'exp_year',
        'default'
    ];

    public function scopeDefaultPayment($query)
    {
        return $query->where('default', 1);
    }
}
