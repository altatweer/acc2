<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم العملة (دينار عراقي، دولار أمريكي..)
            $table->string('code', 3); // رمز العملة (IQD, USD, EUR)
            $table->string('symbol')->nullable(); // رمز العملة ($, د.ع)
            $table->decimal('exchange_rate', 15, 6)->default(1); // سعر الصرف مقابل العملة الافتراضية (مثلا 1 لدينار)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};
