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

        $customers = Account::create([
            'code' => '1200',
            'name' => 'حسابات العملاء',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);

        $fixedAssets = Account::create([
            'code' => '1300',
            'name' => 'أصول ثابتة',
            'parent_id' => $assets->id,
            'type' => 'asset',
            'nature' => null,
            'is_group' => 1,
        ]);

        // حسابات فعلية (Accounts)

        Account::create([
            'code' => '1101',
            'name' => 'صندوق رئيسي دينار',
            'parent_id' => $cash->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
        ]);

        Account::create([
            'code' => '1102',
            'name' => 'حساب بنك رئيسي',
            'parent_id' => $cash->id,
            'type' => 'asset',
            'nature' => 'debit',
            'is_group' => 0,
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

        Account::create([
            'code' => '2101',
            'name' => 'مورد رئيسي',
            'parent_id' => $suppliers->id,
            'type' => 'liability',
            'nature' => 'credit',
            'is_group' => 0,
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

        Account::create([
            'code' => '4101',
            'name' => 'راتب موظف رئيسي',
            'parent_id' => $salaries->id,
            'type' => 'expense',
            'nature' => 'debit',
            'is_group' => 0,
        ]);

        Account::create([
            'code' => '4201',
            'name' => 'فاتورة إيجار مكتب',
            'parent_id' => $officeRent->id,
            'type' => 'expense',
            'nature' => 'debit',
            'is_group' => 0,
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
    }
} 