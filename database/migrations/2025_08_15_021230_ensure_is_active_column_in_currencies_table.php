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
        // التأكد من وجود جدول currencies
        if (!Schema::hasTable('currencies')) {
            return;
        }
        
        Schema::table('currencies', function (Blueprint $table) {
            // إضافة العمود is_active إذا لم يكن موجوداً
            if (!Schema::hasColumn('currencies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('exchange_rate')->comment('العملة نشطة');
            }
            
            // إضافة العمود name_ar إذا لم يكن موجوداً
            if (!Schema::hasColumn('currencies', 'name_ar')) {
                $table->string('name_ar', 255)->after('name')->nullable()->comment('اسم العملة بالعربية');
            }
            
            // إضافة العمود decimal_places إذا لم يكن موجوداً
            if (!Schema::hasColumn('currencies', 'decimal_places')) {
                $table->tinyInteger('decimal_places')->default(2)->after('symbol')->comment('عدد الخانات العشرية');
            }
        });
        
        // تحديث العملات الموجودة لتكون نشطة
        \DB::table('currencies')->update(['is_active' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('currencies')) {
            return;
        }
        
        Schema::table('currencies', function (Blueprint $table) {
            if (Schema::hasColumn('currencies', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('currencies', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('currencies', 'decimal_places')) {
                $table->dropColumn('decimal_places');
            }
        });
    }
};