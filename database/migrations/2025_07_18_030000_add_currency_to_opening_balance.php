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
        Schema::table('accounts', function (Blueprint $table) {
            // إضافة حقل العملة للرصيد الافتتاحي
            $table->string('opening_balance_currency', 3)->default('IQD')->after('opening_balance')
                ->comment('عملة الرصيد الافتتاحي');
            
            // إضافة فهرس للعملة
            $table->index('opening_balance_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex(['opening_balance_currency']);
            $table->dropColumn('opening_balance_currency');
        });
    }
}; 