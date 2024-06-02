<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'amount',
        'balance_transaction_id',
        'currency',
        'destination',
        'type',
        'source_object',
        'reconciliation_status',
        'expected_arrives_at',
        'provider_created_at'
    ];
}
