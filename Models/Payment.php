<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payout_id',
        'user_id',
        'recipient_id',
        'recurring_payment_id',
        'provider_transaction_id',
        'ref_no',
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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_payment_amount',
        'formatted_estimated_platform_fee',
        'formatted_estimated_provider_fee',
        'formatted_total_fee',
        'formatted_total_amount'
    ];

    /**
     * Cast the payment amount attribute.
     *
     * @return Attribute
     */
    protected function paymentAmount(): Attribute
    {
        return $this->normalizeAmount();
    }

    /**
     * Cast the estimated platform fee attribute.
     *
     * @return Attribute
     */
    protected function estimatedPlatformFee(): Attribute
    {
        return $this->normalizeAmount();
    }

    /**
     * Cast the estimated provider fee attribute.
     *
     * @return Attribute
     */
    protected function estimatedProviderFee(): Attribute
    {
        return $this->normalizeAmount();
    }

    /**
     * Cast the total amount attribute.
     *
     * @return Attribute
     */
    protected function totalAmount(): Attribute
    {
        return $this->normalizeAmount();
    }


    /**
     * Cast the formatted payment amount attribute.
     *
     * @return Attribute
     */
    protected function formattedPaymentAmount(): Attribute
    {
        return $this->formatAmount($this->payment_amount);
    }

    /**
     * Cast the formatted estimated platform fee attribute.
     *
     * @return Attribute
     */
    protected function formattedEstimatedPlatformFee(): Attribute
    {
        return $this->formatAmount($this->estimated_platform_fee);
    }

    /**
     * Cast the formatted estimated provider fee attribute.
     *
     * @return Attribute
     */
    protected function formattedEstimatedProviderFee(): Attribute
    {
        return $this->formatAmount($this->estimated_provider_fee);
    }

    /**
     * Cast the formatted total amount attribute.
     *
     * @return Attribute
     */
    protected function formattedTotalAmount(): Attribute
    {
        return $this->formatAmount($this->total_amount);
    }

    /**
     * Cast the formatted total fee attribute.
     *
     * @return Attribute
     */
    protected function formattedTotalFee(): Attribute
    {
        return $this->formatAmount($this->estimated_platform_fee + $this->estimated_provider_fee);
    }

    /**
     * Normalize the amount attribute.
     *
     * @return Attribute
     */
    private function normalizeAmount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }

    /**
     * Format the amount attribute.
     *
     * @param int $value The value to format.
     *
     * @return Attribute
     */
    private function formatAmount(int $value): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($value, 2)
        );
    }
}
