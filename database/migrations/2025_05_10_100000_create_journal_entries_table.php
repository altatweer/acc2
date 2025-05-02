<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description')->nullable();
            $table->string('source_type')->nullable(); // invoice, voucher, manual
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('currency', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
}; 