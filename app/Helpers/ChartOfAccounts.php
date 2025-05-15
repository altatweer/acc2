<?php

namespace App\Helpers;

class ChartOfAccounts
{
    public static function getArabicChart($currency)
    {
        return [
            // الأصول المتداولة
            ['name' => 'الأصول المتداولة', 'code' => '1100', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'الصندوق الرئيسي', 'code' => '1101', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency],
            ['name' => 'الصناديق الفرعية', 'code' => '1102', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency],
            ['name' => 'البنوك', 'code' => '1200', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'البنك الرئيسي', 'code' => '1201', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'حسابات بنكية أخرى', 'code' => '1202', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'العملاء', 'code' => '1301', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'المخزون', 'code' => '1401', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'أوراق القبض', 'code' => '1501', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات مدفوعة مقدماً', 'code' => '1601', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // الأصول الثابتة
            ['name' => 'الأصول الثابتة', 'code' => '1700', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'الأراضي', 'code' => '1701', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'المباني', 'code' => '1702', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'الأثاث', 'code' => '1703', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'المركبات', 'code' => '1704', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'أجهزة ومعدات', 'code' => '1705', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مجمع إهلاك الأصول', 'code' => '1706', 'is_group' => 0, 'type' => 'asset', 'nature' => 'credit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            // الالتزامات المتداولة
            ['name' => 'الالتزامات المتداولة', 'code' => '2100', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'الموردون', 'code' => '2101', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'أوراق الدفع', 'code' => '2102', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'قروض قصيرة الأجل', 'code' => '2103', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات مستحقة', 'code' => '2104', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'ضرائب مستحقة', 'code' => '2105', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'رواتب مستحقة الدفع', 'code' => '2106', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'خصومات مستحقة للموظفين', 'code' => '2201', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // الالتزامات طويلة الأجل
            ['name' => 'الالتزامات طويلة الأجل', 'code' => '2300', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'قروض طويلة الأجل', 'code' => '2301', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2300', 'is_cash_box' => 0, 'currency' => $currency],
            // حقوق الملكية
            ['name' => 'رأس المال', 'code' => '3100', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'الأرباح المحتجزة', 'code' => '3200', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مسحوبات شخصية', 'code' => '3300', 'is_group' => 0, 'type' => 'equity', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // الإيرادات
            ['name' => 'المبيعات', 'code' => '4100', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'إيرادات الخدمات', 'code' => '4200', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'خصم مسموح به', 'code' => '4300', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مردودات المبيعات', 'code' => '4400', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // المصروفات
            ['name' => 'تكلفة البضاعة المباعة', 'code' => '5100', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'المصروفات التشغيلية', 'code' => '5200', 'is_group' => 1, 'type' => 'expense', 'parent_code' => null],
            ['name' => 'الرواتب والأجور', 'code' => '4101', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'الإيجار', 'code' => '5201', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'الكهرباء والماء', 'code' => '5202', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات إدارية وعمومية', 'code' => '5203', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات تسويق', 'code' => '5204', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات صيانة', 'code' => '5205', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات نقل', 'code' => '5206', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات اتصالات', 'code' => '5207', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات بنكية', 'code' => '5208', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'مصروفات أخرى', 'code' => '5209', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            // الضرائب
            ['name' => 'ضريبة القيمة المضافة', 'code' => '5300', 'is_group' => 0, 'type' => 'expense', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'ضريبة الدخل', 'code' => '5301', 'is_group' => 0, 'type' => 'expense', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
        ];
    }

    public static function getEnglishChart($currency)
    {
        return [
            // Current Assets
            ['name' => 'Current Assets', 'code' => '1100', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'Main Cash', 'code' => '1101', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency],
            ['name' => 'Petty Cash', 'code' => '1102', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1100', 'is_cash_box' => 1, 'currency' => $currency],
            ['name' => 'Banks', 'code' => '1200', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'Main Bank', 'code' => '1201', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Other Bank Accounts', 'code' => '1202', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Customers', 'code' => '1301', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Inventory', 'code' => '1401', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Notes Receivable', 'code' => '1501', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Prepaid Expenses', 'code' => '1601', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Fixed Assets
            ['name' => 'Fixed Assets', 'code' => '1700', 'is_group' => 1, 'type' => 'asset', 'parent_code' => null],
            ['name' => 'Land', 'code' => '1701', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Buildings', 'code' => '1702', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Furniture', 'code' => '1703', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Vehicles', 'code' => '1704', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Equipment', 'code' => '1705', 'is_group' => 0, 'type' => 'asset', 'nature' => 'debit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Accumulated Depreciation', 'code' => '1706', 'is_group' => 0, 'type' => 'asset', 'nature' => 'credit', 'parent_code' => '1700', 'is_cash_box' => 0, 'currency' => $currency],
            // Current Liabilities
            ['name' => 'Current Liabilities', 'code' => '2100', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'Suppliers', 'code' => '2101', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Notes Payable', 'code' => '2102', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Short-term Loans', 'code' => '2103', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Accrued Expenses', 'code' => '2104', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Accrued Taxes', 'code' => '2105', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Employee Payables', 'code' => '2106', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2100', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Employee Deductions Payable', 'code' => '2201', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Long-term Liabilities
            ['name' => 'Long-term Liabilities', 'code' => '2300', 'is_group' => 1, 'type' => 'liability', 'parent_code' => null],
            ['name' => 'Long-term Loans', 'code' => '2301', 'is_group' => 0, 'type' => 'liability', 'nature' => 'credit', 'parent_code' => '2300', 'is_cash_box' => 0, 'currency' => $currency],
            // Equity
            ['name' => 'Capital', 'code' => '3100', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Retained Earnings', 'code' => '3200', 'is_group' => 0, 'type' => 'equity', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Owner Withdrawals', 'code' => '3300', 'is_group' => 0, 'type' => 'equity', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Revenues
            ['name' => 'Sales', 'code' => '4100', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Service Revenue', 'code' => '4200', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Sales Discount', 'code' => '4300', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Sales Returns', 'code' => '4400', 'is_group' => 0, 'type' => 'revenue', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            // Expenses
            ['name' => 'Cost of Goods Sold', 'code' => '5100', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Operating Expenses', 'code' => '5200', 'is_group' => 1, 'type' => 'expense', 'parent_code' => null],
            ['name' => 'Salaries & Wages', 'code' => '4101', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Rent', 'code' => '5201', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Utilities', 'code' => '5202', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Admin & General Expenses', 'code' => '5203', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Marketing Expenses', 'code' => '5204', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Maintenance Expenses', 'code' => '5205', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Transportation Expenses', 'code' => '5206', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Communication Expenses', 'code' => '5207', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Bank Charges', 'code' => '5208', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Other Expenses', 'code' => '5209', 'is_group' => 0, 'type' => 'expense', 'nature' => 'debit', 'parent_code' => '5200', 'is_cash_box' => 0, 'currency' => $currency],
            // Taxes
            ['name' => 'VAT Payable', 'code' => '5300', 'is_group' => 0, 'type' => 'expense', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
            ['name' => 'Income Tax', 'code' => '5301', 'is_group' => 0, 'type' => 'expense', 'nature' => 'credit', 'parent_code' => null, 'is_cash_box' => 0, 'currency' => $currency],
        ];
    }
} 