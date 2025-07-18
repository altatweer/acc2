<?php
/**
 * سكريبت إنشاء الحسابات الأساسية للنظام المحاسبي
 * قم بتشغيل هذا الملف عبر: php create_basic_accounts.php
 */

require_once 'vendor/autoload.php';

// تحميل إعدادات Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\AccountingSetting;

echo "🚀 بدء إنشاء الحسابات الأساسية...\n\n";

try {
    DB::transaction(function() {
        
        // 1. إنشاء مجموعة الأصول الرئيسية
        $assets = Account::firstOrCreate([
            'code' => '1000'
        ], [
            'name' => 'الأصول',
            'parent_id' => null,
            'type' => 'asset',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة الأصول\n";

        // 2. إنشاء مجموعة حسابات العملاء
        $customers_group = Account::firstOrCreate([
            'code' => '1201'
        ], [
            'name' => 'حسابات العملاء',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة حسابات العملاء\n";

        // 3. إنشاء مجموعة الخصوم الرئيسية
        $liabilities = Account::firstOrCreate([
            'code' => '2000'
        ], [
            'name' => 'الخصوم',
            'parent_id' => null,
            'type' => 'liability',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة الخصوم\n";

        // 4. إنشاء مجموعة حسابات الموردين
        $suppliers_group = Account::firstOrCreate([
            'code' => '2101'
        ], [
            'name' => 'حسابات الموردين',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة حسابات الموردين\n";

        // 5. إنشاء مجموعة الإيرادات
        $revenues = Account::firstOrCreate([
            'code' => '4000'
        ], [
            'name' => 'الإيرادات',
            'parent_id' => null,
            'type' => 'revenue',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة الإيرادات\n";

        // 6. إنشاء حساب المبيعات
        $sales_account = Account::firstOrCreate([
            'code' => '4001'
        ], [
            'name' => 'مبيعات عامة',
            'parent_id' => $revenues->id,
            'type' => 'revenue',
            'nature' => 'credit',
            'is_group' => false,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء حساب المبيعات\n";

        // 7. إنشاء مجموعة المصاريف
        $expenses = Account::firstOrCreate([
            'code' => '5000'
        ], [
            'name' => 'المصاريف',
            'parent_id' => null,
            'type' => 'expense',
            'nature' => null,
            'is_group' => true,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء مجموعة المصاريف\n";

        // 8. إنشاء حساب مصاريف الرواتب
        $salary_expense = Account::firstOrCreate([
            'code' => '5001'
        ], [
            'name' => 'مصاريف الرواتب',
            'parent_id' => $expenses->id,
            'type' => 'expense',
            'nature' => 'debit',
            'is_group' => false,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء حساب مصاريف الرواتب\n";

        // 9. إنشاء حساب مستحقات الموظفين
        $employee_payables = Account::firstOrCreate([
            'code' => '2102'
        ], [
            'name' => 'مستحقات الموظفين',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => false,
            'is_cash_box' => false,
            'supports_multi_currency' => true,
            'default_currency' => 'IQD',
            'require_currency_selection' => false,
            'tenant_id' => 1,
        ]);
        echo "✅ تم إنشاء حساب مستحقات الموظفين\n";

        // 10. ربط الحسابات في إعدادات النظام
        AccountingSetting::updateOrCreate(
            ['key' => 'default_customers_account'],
            ['value' => $customers_group->id, 'currency' => null, 'tenant_id' => 1]
        );
        
        AccountingSetting::updateOrCreate(
            ['key' => 'default_suppliers_account'],
            ['value' => $suppliers_group->id, 'currency' => null, 'tenant_id' => 1]
        );
        
        AccountingSetting::updateOrCreate(
            ['key' => 'default_sales_account'],
            ['value' => $sales_account->id, 'currency' => null, 'tenant_id' => 1]
        );
        
        AccountingSetting::updateOrCreate(
            ['key' => 'salary_expense_account'],
            ['value' => $salary_expense->id, 'currency' => null, 'tenant_id' => 1]
        );
        
        AccountingSetting::updateOrCreate(
            ['key' => 'employee_payables_account'],
            ['value' => $employee_payables->id, 'currency' => null, 'tenant_id' => 1]
        );

        echo "✅ تم ربط الحسابات في إعدادات النظام\n";
    });

    echo "\n🎉 تم إنشاء جميع الحسابات الأساسية بنجاح!\n";
    echo "📋 الحسابات المُنشأة:\n";
    echo "   - مجموعة حسابات العملاء (1201)\n";
    echo "   - مجموعة حسابات الموردين (2101)\n";
    echo "   - حساب المبيعات العامة (4001)\n";
    echo "   - حساب مصاريف الرواتب (5001)\n";
    echo "   - حساب مستحقات الموظفين (2102)\n\n";
    echo "✨ يمكنك الآن إنشاء العملاء والموردين!\n";

} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}
?> 