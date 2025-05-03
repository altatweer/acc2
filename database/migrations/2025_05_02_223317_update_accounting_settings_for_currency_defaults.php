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
        Schema::dropIfExists('accounting_settings');
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3); // IQD, USD, ...
            $table->unsignedBigInteger('sales_account_id')->nullable();
            $table->unsignedBigInteger('purchases_account_id')->nullable();
            $table->unsignedBigInteger('receivables_account_id')->nullable();
            $table->unsignedBigInteger('payables_account_id')->nullable();
            $table->unsignedBigInteger('expenses_account_id')->nullable();
            $table->unsignedBigInteger('liabilities_account_id')->nullable();
            $table->unsignedBigInteger('deductions_account_id')->nullable();
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
