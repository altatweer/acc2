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
        // التأكد من وجود جدول accounts قبل تعديله
        if (!Schema::hasTable('accounts')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            // إضافة دعم العملات المتعددة إذا لم يكن موجوداً
            if (!Schema::hasColumn('accounts', 'supports_multi_currency')) {
                $table->boolean('supports_multi_currency')->default(true)->after('is_cash_box')
                    ->comment('يدعم العملات المتعددة');
            }
            
            if (!Schema::hasColumn('accounts', 'default_currency')) {
                $table->string('default_currency', 3)->default('IQD')->after('supports_multi_currency')
                    ->comment('العملة الافتراضية للحساب');
            }
            
            if (!Schema::hasColumn('accounts', 'require_currency_selection')) {
                $table->boolean('require_currency_selection')->default(false)->after('default_currency')
                    ->comment('يتطلب تحديد العملة في كل معاملة');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                $columns = ['supports_multi_currency', 'default_currency', 'require_currency_selection'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('accounts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
