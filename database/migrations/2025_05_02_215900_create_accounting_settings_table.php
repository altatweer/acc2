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
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_account_id')->nullable(); // حساب المبيعات الافتراضي
            $table->unsignedBigInteger('purchases_account_id')->nullable(); // حساب المشتريات الافتراضي
            $table->unsignedBigInteger('receivables_account_id')->nullable(); // حساب العملاء الافتراضي
            $table->unsignedBigInteger('payables_account_id')->nullable(); // حساب الموردين الافتراضي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
