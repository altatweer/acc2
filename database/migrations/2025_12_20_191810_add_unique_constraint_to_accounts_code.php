<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // أولاً: إزالة التكرارات قبل إضافة unique constraint
        // سيتم استخدام الأمر accounts:remove-duplicates يدوياً
        
        Schema::table('accounts', function (Blueprint $table) {
            // التحقق من وجود عمود code
            if (Schema::hasColumn('accounts', 'code')) {
                // محاولة حذف الفهارس القديمة إذا كانت موجودة
                try {
                    DB::statement('ALTER TABLE accounts DROP INDEX accounts_code_unique');
                } catch (\Throwable $e) {
                    // تجاهل الخطأ إذا لم يكن الفهرس موجوداً
                }
                
                try {
                    // حذف unique constraint القديم على code + currency إذا كان موجوداً
                    if (Schema::hasColumn('accounts', 'currency')) {
                        DB::statement('ALTER TABLE accounts DROP INDEX accounts_code_currency_unique');
                    }
                } catch (\Throwable $e) {
                    // تجاهل الخطأ
                }
                
                // إضافة unique constraint على code فقط
                // ملاحظة: يجب التأكد من عدم وجود تكرارات قبل تشغيل هذا
                $table->unique('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'code')) {
                try {
                    $table->dropUnique(['code']);
                } catch (\Throwable $e) {
                    // تجاهل الخطأ
                }
            }
        });
    }
};
