<?php

namespace App\Helpers;

class ChartOfAccounts
{
    public static function getArabicChart($currency)
    {
        return [
            // الأصول المتداولة
            [
                'name' => 'الأصول المتداولة', 'code' => '1100', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null
            ],
            [
                'name' => 'الصندوق الرئيسي', 'code' => '1101', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency
            ],
            [
                'name' => 'البنك الرئيسي', 'code' => '1102', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'العملاء', 'code' => '1103', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'المخزون', 'code' => '1104', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'الأصول الثابتة', 'code' => '1200', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null
            ],
            [
                'name' => 'الأثاث', 'code' => '1201', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'المركبات', 'code' => '1202', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency
            ],
            // الالتزامات المتداولة
            [
                'name' => 'الالتزامات المتداولة', 'code' => '2100', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null
            ],
            [
                'name' => 'الموردون', 'code' => '2101', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'القروض قصيرة الأجل', 'code' => '2102', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'الالتزامات طويلة الأجل', 'code' => '2200', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null
            ],
            [
                'name' => 'قروض طويلة الأجل', 'code' => '2201', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2200', 'is_cash_box' => 0, 'currency' => $currency
            ],
            // حقوق الملكية
            [
                'name' => 'رأس المال', 'code' => '3100', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'الأرباح المحتجزة', 'code' => '3200', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency
            ],
            // الإيرادات
            [
                'name' => 'المبيعات', 'code' => '4100', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'إيرادات الخدمات', 'code' => '4200', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency
            ],
            // المصروفات
            [
                'name' => 'المصروفات التشغيلية', 'code' => '5100', 'is_group' => 1, 'type' => 'expense', 'parent_code' => null
            ],
            [
                'name' => 'الرواتب', 'code' => '5101', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'الإيجار', 'code' => '5102', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5100', 'is_cash_box' => 0, 'currency' => $currency
            ],
            [
                'name' => 'المصروفات الإدارية والعمومية', 'code' => '5200', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency
            ],
        ];
    }

    public static function getEnglishChart($currency)
    {
        return [
            // Current Assets
            ['name' => 'Current Assets', 'code' => '1100', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'Main Cash', 'code' => '1101', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency],
            ['name' => 'Main Bank', 'code' => '1102', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Customers', 'code' => '1103', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Inventory', 'code' => '1104', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Fixed Assets', 'code' => '1200', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'Furniture', 'code' => '1201', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Vehicles', 'code' => '1202', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            // Current Liabilities
            ['name' => 'Current Liabilities', 'code' => '2100', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'Suppliers', 'code' => '2101', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Short-term Loans', 'code' => '2102', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Long-term Liabilities', 'code' => '2200', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'Long-term Loans', 'code' => '2201', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2200', 'is_cash_box' => 0, 'currency' => $currency],
            // Equity
            ['name' => 'Capital', 'code' => '3100', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Retained Earnings', 'code' => '3200', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Revenues
            ['name' => 'Sales', 'code' => '4100', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Service Revenue', 'code' => '4200', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Expenses
            ['name' => 'Operating Expenses', 'code' => '5100', 'is_group' => 1, 'type' => 'expense', 'parent_code' => null],
            ['name' => 'Salaries', 'code' => '5101', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Rent', 'code' => '5102', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Admin & General Expenses', 'code' => '5200', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
        ];
    }
} 