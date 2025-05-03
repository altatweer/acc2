<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountsTableSeeder extends Seeder
{
    public function run()
    {
        // الفئات الرئيسية (GROUPS)

        $assets = Account::create([
            'code' => '1000',
            'name' => 'الأصول',
            'parent_id' => null,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);

        $liabilities = Account::create([
            'code' => '2000',
            'name' => 'الالتزامات',
            'parent_id' => null,
            'type' => 'liability',
            'nature' => null,
            'is_group' => 1,
        ]);

        $revenues = Account::create([
            'code' => '3000',
            'name' => 'الإيرادات',
            'parent_id' => null,
            'type' => 'revenue',
            'nature' => null,
            'is_group' => 1,
        ]);

        $expenses = Account::create([
            'code' => '4000',
            'name' => 'المصاريف',
            'parent_id' => null,
            'type' => 'expense',
            'nature' => null,
            'is_group' => 1,
        ]);

        $equity = Account::create([
            'code' => '5000',
            'name' => 'رأس المال',
            'parent_id' => null,
            'type' => 'equity',
            'nature' => null,
            'is_group' => 1,
        ]);

        // فئات فرعية تحت الأصول
        $cash = Account::create([
            'code' => '1100',
            'name' => 'النقدية',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);
        $banks = Account::create([
            'code' => '1200',
            'name' => 'البنوك',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);
        $customers = Account::create([
            'code' => '1300',
            'name' => 'حسابات العملاء',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);
        $inventory = Account::create([
            'code' => '1400',
            'name' => 'المخزون',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);
        // --- أصول ثابتة ---
        $fixedAssets = Account::create([
            'code' => '1500',
            'name' => 'الأصول الثابتة',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);

        // حسابات فعلية (Accounts)

        $cash_iqd = Account::create([
            'code' => '1101',
            'name' => 'صندوق رئيسي دينار',
            'parent_id' => $cash->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'IQD',
            'is_cash_box' => 1,
        ]);

        $bank_iqd = Account::create([
            'code' => '1201',
            'name' => 'بنك رئيسي دينار',
            'parent_id' => $banks->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        $customer_iqd = Account::create([
            'code' => '1301',
            'name' => 'عميل رئيسي دينار',
            'parent_id' => $customers->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        // فئات تحت الالتزامات
        $suppliers = Account::create([
            'code' => '2100',
            'name' => 'حسابات الموردين',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => null,
            'is_group' => 1,
        ]);
        $taxes = Account::create([
            'code' => '2200',
            'name' => 'الضرائب المستحقة',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => null,
            'is_group' => 1,
        ]);
        // --- التزامات طويلة الأجل ---
        $loans = Account::create([
            'code' => '2300',
            'name' => 'قروض طويلة الأجل',
            'parent_id' => $liabilities->id,
            'type' => 'liability',
            'nature' => null,
            'is_group' => 1,
        ]);

        $supplier_iqd = Account::create([
            'code' => '2101',
            'name' => 'مورد رئيسي دينار',
            'parent_id' => $suppliers->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        // فئات تحت الإيرادات
        $productSales = Account::create([
            'code' => '3100',
            'name' => 'مبيعات المنتجات',
            'parent_id' => $revenues->id,
            'type' => 'revenue',
            'nature' => null,
            'is_group' => 1,
        ]);

        $serviceSales = Account::create([
            'code' => '3200',
            'name' => 'مبيعات الخدمات',
            'parent_id' => $revenues->id,
            'type' => 'revenue',
            'nature' => null,
            'is_group' => 1,
        ]);

        Account::create([
            'code' => '3101',
            'name' => 'مبيعات نقدية',
            'parent_id' => $productSales->id,
            'type' => 'revenue',
            'nature' => 'credit',
            'is_group' => 0,
        ]);

        Account::create([
            'code' => '3201',
            'name' => 'مبيعات بالتقسيط',
            'parent_id' => $serviceSales->id,
            'type' => 'revenue',
            'nature' => 'credit',
            'is_group' => 0,
        ]);

        // فئات تحت المصاريف
        $salaries = Account::create([
            'code' => '4100',
            'name' => 'رواتب الموظفين',
            'parent_id' => $expenses->id,
            'type' => 'expense',
            'nature' => null,
            'is_group' => 1,
        ]);

        $officeRent = Account::create([
            'code' => '4200',
            'name' => 'مصاريف إيجار',
            'parent_id' => $expenses->id,
            'type' => 'expense',
            'nature' => null,
            'is_group' => 1,
        ]);

        $salary_expense_iqd = Account::create([
            'code' => '4101',
            'name' => 'مصروف رواتب دينار',
            'parent_id' => $salaries->id,
            'type' => 'expense',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        $liabilities_iqd = Account::create([
            'code' => '2102',
            'name' => 'ذمم مستحقة للموظفين دينار',
            'parent_id' => $suppliers->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        $deductions_iqd = Account::create([
            'code' => '2201',
            'name' => 'خصومات رواتب دينار',
            'parent_id' => $taxes->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'IQD',
        ]);

        // فئات تحت رأس المال
        Account::create([
            'code' => '5100',
            'name' => 'رأس مال المؤسسين',
            'parent_id' => $equity->id,
            'type' => 'equity',
            'nature' => null,
            'is_group' => 1,
        ]);

        Account::create([
            'code' => '5101',
            'name' => 'رأس مال المالك',
            'parent_id' => $equity->id,
            'type' => 'equity',
            'nature' => 'credit',
            'is_group' => 0,
        ]);

        // --- حسابات فعلية دولار ---
        $cash_usd = Account::create([
            'code' => '1102',
            'name' => 'صندوق رئيسي دولار',
            'parent_id' => $cash->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'USD',
            'is_cash_box' => 1,
        ]);

        $bank_usd = Account::create([
            'code' => '1202',
            'name' => 'بنك رئيسي دولار',
            'parent_id' => $banks->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        $customer_usd = Account::create([
            'code' => '1302',
            'name' => 'عميل رئيسي دولار',
            'parent_id' => $customers->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        $supplier_usd = Account::create([
            'code' => '2103',
            'name' => 'مورد رئيسي دولار',
            'parent_id' => $suppliers->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        $salary_expense_usd = Account::create([
            'code' => '4102',
            'name' => 'مصروف رواتب دولار',
            'parent_id' => $salaries->id,
            'type' => 'expense',
            'nature' => 'debit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        $liabilities_usd = Account::create([
            'code' => '2104',
            'name' => 'ذمم مستحقة للموظفين دولار',
            'parent_id' => $suppliers->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        $deductions_usd = Account::create([
            'code' => '2202',
            'name' => 'خصومات رواتب دولار',
            'parent_id' => $taxes->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
            'currency' => 'USD',
        ]);

        // --- تحديث إعدادات الحسابات الافتراضية ---
        \App\Models\AccountingSetting::updateOrCreate([
            'currency' => 'IQD',
        ], [
            'sales_account_id' => $productSales->id,
            'purchases_account_id' => null,
            'receivables_account_id' => $customer_iqd->id,
            'payables_account_id' => $supplier_iqd->id,
            'expenses_account_id' => $salary_expense_iqd->id,
            'liabilities_account_id' => $liabilities_iqd->id,
            'deductions_account_id' => $deductions_iqd->id,
        ]);
        \App\Models\AccountingSetting::updateOrCreate([
            'currency' => 'USD',
        ], [
            'sales_account_id' => $productSales->id,
            'purchases_account_id' => null,
            'receivables_account_id' => $customer_usd->id,
            'payables_account_id' => $supplier_usd->id,
            'expenses_account_id' => $salary_expense_usd->id,
            'liabilities_account_id' => $liabilities_usd->id,
            'deductions_account_id' => $deductions_usd->id,
        ]);
    }
} 