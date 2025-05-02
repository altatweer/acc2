<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run()
    {
        // الأصول المتداولة
        $currentAssets = Account::firstOrCreate([
            'name' => 'الأصول المتداولة', 'is_group' => 1, 'type' => 'asset', 'parent_id' => null
        ], [
            'code' => '1100', 'nature' => null, 'is_cash_box' => 0
        ]);
        // الأصول الثابتة
        $fixedAssets = Account::firstOrCreate([
            'name' => 'الأصول الثابتة', 'is_group' => 1, 'type' => 'asset', 'parent_id' => null
        ], [
            'code' => '1200', 'nature' => null, 'is_cash_box' => 0
        ]);
        // الالتزامات المتداولة
        $currentLiabilities = Account::firstOrCreate([
            'name' => 'الالتزامات المتداولة', 'is_group' => 1, 'type' => 'liability', 'parent_id' => null
        ], [
            'code' => '2100', 'nature' => null, 'is_cash_box' => 0
        ]);
        // الإيرادات التشغيلية
        $operatingRevenue = Account::firstOrCreate([
            'name' => 'الإيرادات التشغيلية', 'is_group' => 1, 'type' => 'revenue', 'parent_id' => null
        ], [
            'code' => '3100', 'nature' => null, 'is_cash_box' => 0
        ]);
        // المصروفات التشغيلية
        $operatingExpenses = Account::firstOrCreate([
            'name' => 'المصروفات التشغيلية', 'is_group' => 1, 'type' => 'expense', 'parent_id' => null
        ], [
            'code' => '4100', 'nature' => null, 'is_cash_box' => 0
        ]);
        // حقوق الملكية
        $equity = Account::firstOrCreate([
            'name' => 'حقوق الملكية', 'is_group' => 1, 'type' => 'equity', 'parent_id' => null
        ], [
            'code' => '5100', 'nature' => null, 'is_cash_box' => 0
        ]);

        // حسابات فعلية تحت الفئات
        // الأصول المتداولة
        Account::firstOrCreate([
            'name' => 'العملاء', 'is_group' => 0, 'parent_id' => $currentAssets->id
        ], [
            'code' => '1101', 'type' => 'asset', 'nature' => 'debit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        Account::firstOrCreate([
            'name' => 'المخزون', 'is_group' => 0, 'parent_id' => $currentAssets->id
        ], [
            'code' => '1102', 'type' => 'asset', 'nature' => 'debit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        // الأصول الثابتة
        Account::firstOrCreate([
            'name' => 'الأثاث', 'is_group' => 0, 'parent_id' => $fixedAssets->id
        ], [
            'code' => '1201', 'type' => 'asset', 'nature' => 'debit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        // الالتزامات المتداولة
        Account::firstOrCreate([
            'name' => 'الموردون', 'is_group' => 0, 'parent_id' => $currentLiabilities->id
        ], [
            'code' => '2101', 'type' => 'liability', 'nature' => 'credit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        // الإيرادات التشغيلية
        Account::firstOrCreate([
            'name' => 'المبيعات', 'is_group' => 0, 'parent_id' => $operatingRevenue->id
        ], [
            'code' => '3101', 'type' => 'revenue', 'nature' => 'credit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        // المصروفات التشغيلية
        Account::firstOrCreate([
            'name' => 'الرواتب', 'is_group' => 0, 'parent_id' => $operatingExpenses->id
        ], [
            'code' => '4101', 'type' => 'expense', 'nature' => 'debit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        Account::firstOrCreate([
            'name' => 'الإيجار', 'is_group' => 0, 'parent_id' => $operatingExpenses->id
        ], [
            'code' => '4102', 'type' => 'expense', 'nature' => 'debit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
        // حقوق الملكية
        Account::firstOrCreate([
            'name' => 'رأس المال', 'is_group' => 0, 'parent_id' => $equity->id
        ], [
            'code' => '5101', 'type' => 'equity', 'nature' => 'credit', 'currency' => 'IQD', 'is_cash_box' => 0
        ]);
    }
} 