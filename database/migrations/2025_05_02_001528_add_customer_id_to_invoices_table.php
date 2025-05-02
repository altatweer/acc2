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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('invoice_number');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->after('invoice_number');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
