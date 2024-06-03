<?php

use Modules\Payment\Models\Payout;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create((new Payout)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('provider_id')->comment('payout transaction id');
            $table->bigInteger('amount');
            $table->string('balance_transaction_id');
            $table->char('currency', 3);
            $table->string('destination');
            $table->string('type');
            $table->json('source_object');
            $table->string('reconciliation_status');
            $table->timestamp('expected_arrives_at');
            $table->timestamp('provider_created_at');
            $table->timestamps();

            $table->index([
                'provider_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Payout)->getTable());
    }
};
