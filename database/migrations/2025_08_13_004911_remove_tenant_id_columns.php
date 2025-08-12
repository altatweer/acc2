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
        // قائمة الجداول التي تحتوي على tenant_id
        $tablesWithTenantId = [
            'users',
            'accounts', 
            'currencies',
            'customers',
            'suppliers',
            'employees',
            'branches',
            'items',
            'invoices',
            'invoice_items',
            'vouchers',
            'transactions',
            'journal_entries',
            'journal_entry_lines',
            'salary_batches',
            'salary_payments',
            'salaries',
            'account_balances',
            'accounting_settings',
            'settings',
            'print_settings'
        ];
        
        echo "🗑️ بدء إزالة أعمدة tenant_id من قاعدة البيانات...\n";
        
        foreach ($tablesWithTenantId as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (Schema::hasColumn($tableName, 'tenant_id')) {
                    // التحقق من وجود foreign key أولاً
                    $foreignKeyExists = false;
                    try {
                        $foreignKeys = \DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'tenant_id' AND REFERENCED_TABLE_NAME IS NOT NULL", [$tableName]);
                        $foreignKeyExists = !empty($foreignKeys);
                    } catch (\Exception $e) {
                        // تجاهل الخطأ
                    }
                    
                    Schema::table($tableName, function (Blueprint $table) use ($tableName, $foreignKeyExists) {
                        // حذف foreign key constraint أولاً إذا كان موجود
                        if ($foreignKeyExists) {
                            try {
                                $table->dropForeign("{$tableName}_tenant_id_foreign");
                            } catch (\Exception $e) {
                                // تجاهل الخطأ
                            }
                        }
                        
                        // حذف العمود
                        $table->dropColumn('tenant_id');
                    });
                    echo "✅ تم حذف tenant_id من جدول: {$tableName}\n";
                } else {
                    echo "⚪ جدول {$tableName} لا يحتوي على tenant_id\n";
                }
            } else {
                echo "❌ جدول {$tableName} غير موجود\n";
            }
        }
        
        // حذف جداول tenant المرتبطة أولاً
        $tenantRelatedTables = ['tenant_features'];
        foreach ($tenantRelatedTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                echo "🗑️ تم حذف جدول: $table\n";
            }
        }
        
        // حذف جدول tenants بالكامل
        if (Schema::hasTable('tenants')) {
            Schema::dropIfExists('tenants');
            echo "🗑️ تم حذف جدول tenants بالكامل\n";
        }
        
        echo "🎉 انتهت إزالة أعمدة tenant_id بنجاح!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // في حالة الحاجة للعودة، يمكن إضافة tenant_id مرة أخرى
        $tablesWithTenantId = [
            'users',
            'accounts', 
            'currencies',
            'customers',
            'suppliers',
            'employees',
            'branches',
            'items',
            'invoices',
            'invoice_items',
            'vouchers',
            'transactions',
            'journal_entries',
            'journal_entry_lines',
            'salary_batches',
            'salary_payments',
            'salaries',
            'account_balances',
            'accounting_settings',
            'settings',
            'print_settings'
        ];
        
        foreach ($tablesWithTenantId as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('tenant_id')->default(1)->after('id');
                });
            }
        }
        
        // إعادة إنشاء جدول tenants
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('subdomain')->unique()->nullable();
            $table->string('contact_email');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
