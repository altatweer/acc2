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
        // التأكد من وجود جدول invoice_items
        if (!Schema::hasTable('invoice_items')) {
            return;
        }
        
        Schema::table('invoice_items', function (Blueprint $table) {
            // إضافة العمود currency إذا لم يكن موجوداً
            if (!Schema::hasColumn('invoice_items', 'currency')) {
                $table->string('currency', 3)->default('IQD')->after('line_total')->comment('عملة البند');
            }
            
            // إضافة العمود exchange_rate إذا لم يكن موجوداً
            if (!Schema::hasColumn('invoice_items', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 10)->default(1.0000000000)->after('currency')->comment('سعر الصرف المستخدم');
            }
            
            // إضافة العمود base_currency_total إذا لم يكن موجوداً
            if (!Schema::hasColumn('invoice_items', 'base_currency_total')) {
                $table->decimal('base_currency_total', 18, 4)->after('exchange_rate')->comment('المجموع بالعملة الأساسية');
            }
        });
        
        // تحديث البيانات الموجودة
        \DB::statement("UPDATE invoice_items SET currency = 'IQD' WHERE currency IS NULL");
        \DB::statement("UPDATE invoice_items SET exchange_rate = 1.0000000000 WHERE exchange_rate IS NULL");
        \DB::statement("UPDATE invoice_items SET base_currency_total = line_total WHERE base_currency_total IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('invoice_items')) {
            return;
        }
        
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('invoice_items', 'exchange_rate')) {
                $table->dropColumn('exchange_rate');
            }
            if (Schema::hasColumn('invoice_items', 'base_currency_total')) {
                $table->dropColumn('base_currency_total');
            }
        });
    }
};