<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Payment\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create((new PaymentMethod)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->default(null)->constrained()->onDelete('cascade'); //TODO: remove nullable later
            $table->string('payment_method_id', 100);
            $table->string('type', 50);
            $table->char('last', 4);
            $table->string('brand', 50);
            $table->tinyInteger('exp_month');
            $table->tinyInteger('exp_year');
            $table->boolean('default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new PaymentMethod)->getTable());
    }
};
