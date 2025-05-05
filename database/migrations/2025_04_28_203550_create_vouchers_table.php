<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->enum('type', ['receipt', 'payment', 'transfer']);
            $table->date('date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->string('recipient_name')->nullable();
            $table->enum('status', ['active', 'canceled'])->default('active');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
