<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('provider_transaction_id', 100)->nullable();
            $table->double('payment_amount')->nullable();
            $table->double('provider_fee')->nullable();
            $table->double('platform_fee')->nullable();
            $table->double('total_amount')->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('source')->nullable();
            $table->string('status', 15)->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
