<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['deposit', 'withdraw', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IQD');
            $table->decimal('exchange_rate', 15, 6)->default(1); // سعر الصرف مقابل العملة الأساسية
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('target_account_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('target_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};