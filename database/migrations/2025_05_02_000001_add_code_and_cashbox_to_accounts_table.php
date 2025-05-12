<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Only add code column if it does not exist
        if (!Schema::hasColumn('accounts', 'code')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->string('code')->after('name');
            });
        }
        // Only add is_cash_box column if it does not exist
        if (!Schema::hasColumn('accounts', 'is_cash_box')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->boolean('is_cash_box')->default(false)->after('is_group');
            });
        }
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['code', 'is_cash_box']);
        });
    }
}; 