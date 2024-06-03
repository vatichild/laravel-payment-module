<?php

use Modules\Payment\Models\Payment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * TODO: ask if we need to set payment method (saved card) id  in payment table
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create((new Payment)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payout_id')->nullable()
                    ->default(null)->constrained()
                    ->onDelete('set null')
                    ->comment('Application may have multiple payments on single payout');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); //TODO: remove nullable later
            $table->foreignId('recipient_id')->nullable()->default(null)->constrained()->onDelete('cascade');
            $table->string('recurring_payment_id', 100)->nullable();
            $table->string('provider_transaction_id', 100);
            $table->unsignedBigInteger('payment_amount');
            $table->unsignedInteger('actual_platform_fee')->nullable();
            $table->unsignedInteger('estimated_platform_fee');
            $table->unsignedInteger('actual_provider_fee')->nullable();
            $table->unsignedInteger('estimated_provider_fee');
            $table->unsignedInteger('discount_amount')->nullable();
            $table->unsignedBigInteger('total_amount');
            $table->char('currency', 3);
            $table->integer('promo_code_id')->nullable();
            $table->integer('business_account_id')->nullable();
            $table->string('ref_no', 50);
            $table->string('reference')->nullable();
            $table->string('status', 15);
            $table->timestamps();
            $table->index(
                [
                    'provider_transaction_id',
                    'payment_amount',
                    'total_amount',
                    'reference',
                    'created_at'
                ],
                'transaction_payment_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Payment)->getTable());
    }
};
