<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
       Schema::table('accounts', function (Blueprint $table) {
           $table->boolean('is_cash_box')->default(0)->after('nature');
       });
   }

   public function down()
   {
       Schema::table('accounts', function (Blueprint $table) {
           $table->dropColumn('is_cash_box');
       });
   }
};