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
            $table->unsignedBigInteger('payout_id')->constrained()->onDelete('set null')->nullable()->default(null); //TODO: ask about this
            $table->foreignId('user_id')->nullable()->default(null)->constrained()->onDelete('cascade'); //TODO: remove nullable later
            $table->foreignId('recipient_id')->nullable()->default(null)->constrained()->onDelete('cascade'); //TODO: remove nullable later
            $table->string('recurring_payment_id', 100)->nullable();
            $table->string('provider_transaction_id', 100)->nullable();
            $table->double('payment_amount')->nullable();
            $table->double('actual_platform_fee')->nullable();
            $table->double('estimated_platform_fee')->nullable(); //TODO: ask about this
            $table->double('actual_provider_fee')->nullable();
            $table->double('estimated_provider_fee')->nullable(); //TODO: ask about this
            $table->double('discount_amount')->nullable();
            $table->double('total_amount')->nullable();
            $table->char('currency', 3)->nullable();
            $table->integer('promo_code_id')->nullable(); //TODO: ask about this
            $table->integer('business_account_id')->nullable(); //TODO: ask about this
            $table->string('reference')->nullable(); //TODO: ask about this
            $table->string('status', 15)->nullable(); //TODO: ask about this
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
