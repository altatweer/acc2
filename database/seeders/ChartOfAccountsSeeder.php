<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run()
    {
        // حذف جميع الحسابات الحالية
        Account::query()->delete();

        // الأصول
        $assets = Account::create([
            'name' => 'الأصول', 'code' => '1000', 'is_group' => 1, 'type' => 'asset', 'parent_id' => null
        ]);
        $currentAssets = Account::create([
            'name' => 'الأصول المتداولة', 'code' => '1100', 'is_group' => 1, 'type' => 'asset', 'parent_id' => $assets->id
        ]);
        $cash = Account::create([
            'name' => 'الصندوق الرئيسي', 'code' => '1101', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $currentAssets->id, 'is_cash_box' => 1, 'currency' => 'IQD'
        ]);
        $bank = Account::create([
            'name' => 'البنك الرئيسي', 'code' => '1102', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $currentAssets->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $customers = Account::create([
            'name' => 'العملاء', 'code' => '1103', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $currentAssets->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $inventory = Account::create([
            'name' => 'المخزون', 'code' => '1104', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $currentAssets->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $fixedAssets = Account::create([
            'name' => 'الأصول الثابتة', 'code' => '1200', 'is_group' => 1, 'type' => 'asset', 'parent_id' => $assets->id
        ]);
        $furniture = Account::create([
            'name' => 'الأثاث', 'code' => '1201', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $fixedAssets->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $vehicles = Account::create([
            'name' => 'المركبات', 'code' => '1202', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_id' => $fixedAssets->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);

        // الالتزامات
        $liabilities = Account::create([
            'name' => 'الالتزامات', 'code' => '2000', 'is_group' => 1, 'type' => 'liability', 'parent_id' => null
        ]);
        $currentLiabilities = Account::create([
            'name' => 'الالتزامات المتداولة', 'code' => '2100', 'is_group' => 1, 'type' => 'liability', 'parent_id' => $liabilities->id
        ]);
        $suppliers = Account::create([
            'name' => 'الموردون', 'code' => '2101', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_id' => $currentLiabilities->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $loans = Account::create([
            'name' => 'القروض قصيرة الأجل', 'code' => '2102', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_id' => $currentLiabilities->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $longTermLiabilities = Account::create([
            'name' => 'الالتزامات طويلة الأجل', 'code' => '2200', 'is_group' => 1, 'type' => 'liability', 'parent_id' => $liabilities->id
        ]);
        $longTermLoans = Account::create([
            'name' => 'قروض طويلة الأجل', 'code' => '2201', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_id' => $longTermLiabilities->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);

        // حقوق الملكية
        $equity = Account::create([
            'name' => 'حقوق الملكية', 'code' => '3000', 'is_group' => 1, 'type' => 'equity', 'parent_id' => null
        ]);
        $capital = Account::create([
            'name' => 'رأس المال', 'code' => '3100', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_id' => $equity->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $retainedEarnings = Account::create([
            'name' => 'الأرباح المحتجزة', 'code' => '3200', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_id' => $equity->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);

        // الإيرادات
        $revenues = Account::create([
            'name' => 'الإيرادات', 'code' => '4000', 'is_group' => 1, 'type' => 'revenue', 'parent_id' => null
        ]);
        $sales = Account::create([
            'name' => 'المبيعات', 'code' => '4100', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_id' => $revenues->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $serviceRevenue = Account::create([
            'name' => 'إيرادات الخدمات', 'code' => '4200', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_id' => $revenues->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);

        // المصروفات
        $expenses = Account::create([
            'name' => 'المصروفات', 'code' => '5000', 'is_group' => 1, 'type' => 'expense', 'parent_id' => null
        ]);
        $operatingExpenses = Account::create([
            'name' => 'المصروفات التشغيلية', 'code' => '5100', 'is_group' => 1, 'type' => 'expense', 'parent_id' => $expenses->id
        ]);
        $salaries = Account::create([
            'name' => 'الرواتب', 'code' => '5101', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_id' => $operatingExpenses->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $rent = Account::create([
            'name' => 'الإيجار', 'code' => '5102', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_id' => $operatingExpenses->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
        $adminExpenses = Account::create([
            'name' => 'المصروفات الإدارية والعمومية', 'code' => '5200', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_id' => $expenses->id, 'is_cash_box' => 0, 'currency' => 'IQD'
        ]);
    }
} 