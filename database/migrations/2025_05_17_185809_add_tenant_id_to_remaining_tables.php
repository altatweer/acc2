<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * قائمة بالجداول المتبقية التي سيضاف لها حقل tenant_id
     */
    protected $tables = [
        // جداول التقارير والإحصائيات المستقبلية
        'reports',
        'report_templates',
        'financial_periods',
        'fiscal_years',
        
        // جداول أخرى قد تكون موجودة في النظام
        'invoice_payments',
        'languages',
        'tax_rates',
        'expense_categories',
        'expenses',
        'user_preferences',
        'notifications',
        'templates',
        'documents',
        'document_types',
        
        // أي جداول إضافية للنظام
        // يمكن إضافة المزيد من الجداول هنا حسب احتياج النظام
    ];

    /**
     * قائمة بالجداول الخاصة (جداول العلاقات) التي لا تحتوي على حقل id
     * هذه الجداول تستخدم مفاتيح مركبة وليس لها مفتاح أساسي تسلسلي
     */
    protected $pivotTables = [
        'role_has_permissions',
        'account_user',
        'model_has_roles', 
        'model_has_permissions'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // إضافة حقل tenant_id لجميع الجداول المتبقية
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    // إضافة حقل tenant_id مع السماح بالقيم الفارغة حالياً
                    // سيكون nullable الآن وسيتم تعديله لاحقاً عند تفعيل نظام متعدد المستأجرين
                    $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
                });
            }
        }
        
        // معالجة جداول العلاقات الخاصة (جداول العلاقات متعددة-متعددة)
        foreach ($this->pivotTables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->nullable()->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // إزالة حقل tenant_id من جميع الجداول
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
        
        // إزالة tenant_id من الجداول الخاصة (جداول العلاقات)
        foreach ($this->pivotTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
