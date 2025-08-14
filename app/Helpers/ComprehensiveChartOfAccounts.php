<?php

namespace App\Helpers;

class ComprehensiveChartOfAccounts
{
    /**
     * الحصول على الشجرة المحاسبية الشاملة باللغة العربية (مطابقة للنظام الحالي)
     */
    public static function getArabicChart($currency)
    {
        return [
            // ===== 1️⃣ الأصول (Assets) 1000-1999 =====
            
            // الأصول المتداولة (1000-1499)
            ['code' => '1000', 'name' => 'الأصول المتداولة', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            
            // النقدية والبنوك (1100-1199)
            ['code' => '1100', 'name' => 'النقدية والبنوك', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1101', 'name' => 'الصندوق الرئيسي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1102', 'name' => 'صندوق المبيعات', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1103', 'name' => 'صندوق المصروفات الصغيرة', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1110', 'name' => 'البنك المركزي العراقي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1111', 'name' => 'بنك بغداد', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1112', 'name' => 'البنك التجاري العراقي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1113', 'name' => 'البنك الأهلي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1114', 'name' => 'بنوك دولية', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // العملاء والذمم المدينة (1200-1299)
            ['code' => '1200', 'name' => 'العملاء والذمم المدينة', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1201', 'name' => 'العملاء المحليون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1202', 'name' => 'العملاء الدوليون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1203', 'name' => 'العملاء الحكوميون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1210', 'name' => 'أوراق القبض', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1220', 'name' => 'مدفوعات مقدمة للموردين', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1230', 'name' => 'سلف الموظفين', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // المخزون والبضائع (1300-1399)
            ['code' => '1300', 'name' => 'المخزون والبضائع', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1301', 'name' => 'بضاعة جاهزة للبيع', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1302', 'name' => 'بضاعة تحت التشغيل', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1303', 'name' => 'مواد خام ومستلزمات', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1310', 'name' => 'لوازم مكتبية', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // الأصول الثابتة (1500-1999)
            ['code' => '1500', 'name' => 'الأصول الثابتة', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1501', 'name' => 'الأراضي', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1502', 'name' => 'المباني والمنشآت', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1503', 'name' => 'المعدات والآلات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1504', 'name' => 'الأثاث والمفروشات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1505', 'name' => 'المركبات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1510', 'name' => 'مجمع إهلاك المباني', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1511', 'name' => 'مجمع إهلاك المعدات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1512', 'name' => 'مجمع إهلاك الأثاث', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1513', 'name' => 'مجمع إهلاك المركبات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 2️⃣ الخصوم (Liabilities) 2000-2999 =====
            
            // الخصوم المتداولة (2000-2499)
            ['code' => '2000', 'name' => 'الخصوم المتداولة', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            
            // الموردون والذمم الدائنة (2100-2199)
            ['code' => '2100', 'name' => 'الموردون والذمم الدائنة', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2101', 'name' => 'الموردون المحليون', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2102', 'name' => 'الموردون الدوليون', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2103', 'name' => 'أوراق الدفع', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2110', 'name' => 'مقبوضات مقدمة من العملاء', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // الرواتب والمزايا (2200-2299)
            ['code' => '2200', 'name' => 'الرواتب والمزايا', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2201', 'name' => 'رواتب مستحقة الدفع', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2202', 'name' => 'مكافآت مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2203', 'name' => 'بدلات مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2210', 'name' => 'ضرائب مستحقة الدفع', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2211', 'name' => 'ضريبة القيمة المضافة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // خصومات الموظفين (2300-2399)
            ['code' => '2300', 'name' => 'خصومات الموظفين', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2301', 'name' => 'سلف الموظفين', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2302', 'name' => 'خصم التأمين الصحي', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2303', 'name' => 'خصم التأمين الاجتماعي', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // القروض قصيرة الأجل (2400-2499)
            ['code' => '2400', 'name' => 'قروض قصيرة الأجل', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2401', 'name' => 'قروض بنكية قصيرة الأجل', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2402', 'name' => 'تسهيلات ائتمانية', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // الخصوم طويلة الأجل (2500-2999)
            ['code' => '2500', 'name' => 'الخصوم طويلة الأجل', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2501', 'name' => 'قروض بنكية طويلة الأجل', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2520', 'name' => 'مخصص نهاية الخدمة', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 3️⃣ حقوق الملكية (Equity) 3000-3999 =====
            ['code' => '3000', 'name' => 'حقوق الملكية', 'parent_code' => '', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            
            // رأس المال
            ['code' => '3100', 'name' => 'رأس المال', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3101', 'name' => 'رأس المال المدفوع', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3102', 'name' => 'رأس المال المصرح به', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // الاحتياطيات
            ['code' => '3200', 'name' => 'الاحتياطيات', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3201', 'name' => 'الاحتياطي القانوني', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3202', 'name' => 'الاحتياطي الاختياري', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // الأرباح والخسائر
            ['code' => '3300', 'name' => 'الأرباح والخسائر', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3301', 'name' => 'الأرباح المحتجزة', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3302', 'name' => 'أرباح العام الحالي', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3310', 'name' => 'مسحوبات شخصية', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 4️⃣ الإيرادات (Revenue) 4000-4999 =====
            ['code' => '4000', 'name' => 'الإيرادات', 'parent_code' => '', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            
            // إيرادات التشغيل الأساسية
            ['code' => '4100', 'name' => 'إيرادات التشغيل الأساسية', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4101', 'name' => 'المبيعات المحلية', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4102', 'name' => 'المبيعات التصديرية', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4103', 'name' => 'إيرادات الخدمات', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4110', 'name' => 'خصم مسموح به', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4111', 'name' => 'مردودات المبيعات', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // الإيرادات الأخرى
            ['code' => '4200', 'name' => 'الإيرادات الأخرى', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4201', 'name' => 'إيرادات إيجارات', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4202', 'name' => 'إيرادات فوائد', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 5️⃣ المصروفات (Expenses) 5000-5999 =====
            ['code' => '5000', 'name' => 'المصروفات', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            
            // تكلفة البضاعة المباعة
            ['code' => '5100', 'name' => 'تكلفة البضاعة المباعة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5101', 'name' => 'مشتريات البضاعة', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5102', 'name' => 'مصروفات الشراء', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5110', 'name' => 'خصم مكتسب', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // مصروفات الرواتب والأجور
            ['code' => '5200', 'name' => 'مصروفات الرواتب والأجور', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5201', 'name' => 'الرواتب الأساسية', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5202', 'name' => 'البدلات والمكافآت', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5203', 'name' => 'مساهمة صاحب العمل في التأمينات', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // المصروفات التشغيلية العامة
            ['code' => '5300', 'name' => 'المصروفات التشغيلية العامة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5301', 'name' => 'الإيجار', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5302', 'name' => 'الكهرباء والماء', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5303', 'name' => 'الاتصالات والإنترنت', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5304', 'name' => 'الصيانة والإصلاح', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5305', 'name' => 'التأمين', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5310', 'name' => 'اللوازم المكتبية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5311', 'name' => 'مصروفات النقل والمواصلات', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // مصروفات الإهلاك
            ['code' => '5400', 'name' => 'مصروفات الإهلاك', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5401', 'name' => 'إهلاك المباني', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5402', 'name' => 'إهلاك المعدات والآلات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5403', 'name' => 'إهلاك الأثاث والمفروشات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // المصروفات المالية
            ['code' => '5500', 'name' => 'المصروفات المالية', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5501', 'name' => 'فوائد القروض', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5502', 'name' => 'رسوم بنكية', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // الضرائب والرسوم
            ['code' => '5600', 'name' => 'الضرائب والرسوم', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5601', 'name' => 'ضريبة الدخل', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5602', 'name' => 'ضريبة القيمة المضافة', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
        ];
    }

    /**
     * الحصول على الشجرة المحاسبية الشاملة باللغة الإنجليزية (مطابقة للنظام الحالي)
     */
    public static function getEnglishChart($currency)
    {
        return [
            // ===== 1️⃣ Assets 1000-1999 =====
            
            // Current Assets (1000-1499)
            ['code' => '1000', 'name' => 'Current Assets', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            
            // Cash and Banks (1100-1199)
            ['code' => '1100', 'name' => 'Cash and Banks', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1101', 'name' => 'Main Cash', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1102', 'name' => 'Sales Cash', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1103', 'name' => 'Petty Cash', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true, 'default_currency' => $currency],
            ['code' => '1110', 'name' => 'Central Bank of Iraq', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1111', 'name' => 'Bank of Baghdad', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1112', 'name' => 'Commercial Bank of Iraq', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1113', 'name' => 'National Bank', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1114', 'name' => 'International Banks', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Accounts Receivable (1200-1299)
            ['code' => '1200', 'name' => 'Accounts Receivable', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1201', 'name' => 'Local Customers', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1202', 'name' => 'International Customers', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1203', 'name' => 'Government Customers', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1210', 'name' => 'Notes Receivable', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1220', 'name' => 'Prepaid to Suppliers', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1230', 'name' => 'Employee Advances', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Inventory (1300-1399)
            ['code' => '1300', 'name' => 'Inventory', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1301', 'name' => 'Finished Goods', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1302', 'name' => 'Work in Process', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1303', 'name' => 'Raw Materials', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1310', 'name' => 'Office Supplies', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Fixed Assets (1500-1999)
            ['code' => '1500', 'name' => 'Fixed Assets', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1501', 'name' => 'Land', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1502', 'name' => 'Buildings', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1503', 'name' => 'Equipment & Machinery', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1504', 'name' => 'Furniture & Fixtures', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1505', 'name' => 'Vehicles', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1510', 'name' => 'Accumulated Depreciation - Buildings', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1511', 'name' => 'Accumulated Depreciation - Equipment', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1512', 'name' => 'Accumulated Depreciation - Furniture', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '1513', 'name' => 'Accumulated Depreciation - Vehicles', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 2️⃣ Liabilities 2000-2999 =====
            
            // Current Liabilities (2000-2499)
            ['code' => '2000', 'name' => 'Current Liabilities', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            
            // Accounts Payable (2100-2199)
            ['code' => '2100', 'name' => 'Accounts Payable', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2101', 'name' => 'Local Suppliers', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2102', 'name' => 'International Suppliers', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2103', 'name' => 'Notes Payable', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2110', 'name' => 'Customer Prepayments', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Payroll Liabilities (2200-2299)
            ['code' => '2200', 'name' => 'Payroll Liabilities', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2201', 'name' => 'Salaries Payable', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2202', 'name' => 'Bonuses Payable', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2203', 'name' => 'Allowances Payable', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2210', 'name' => 'Taxes Payable', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2211', 'name' => 'VAT Payable', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Employee Deductions (2300-2399)
            ['code' => '2300', 'name' => 'Employee Deductions', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2301', 'name' => 'Employee Advances', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2302', 'name' => 'Health Insurance Deduction', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2303', 'name' => 'Social Insurance Deduction', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Short-term Loans (2400-2499)
            ['code' => '2400', 'name' => 'Short-term Loans', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2401', 'name' => 'Bank Loans - Short Term', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2402', 'name' => 'Credit Facilities', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Long-term Liabilities (2500-2999)
            ['code' => '2500', 'name' => 'Long-term Liabilities', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2501', 'name' => 'Bank Loans - Long Term', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '2520', 'name' => 'End of Service Provision', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 3️⃣ Equity 3000-3999 =====
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'parent_code' => '', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            
            // Capital
            ['code' => '3100', 'name' => 'Capital', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3101', 'name' => 'Paid-up Capital', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3102', 'name' => 'Authorized Capital', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Reserves
            ['code' => '3200', 'name' => 'Reserves', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3201', 'name' => 'Legal Reserve', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3202', 'name' => 'Voluntary Reserve', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Earnings
            ['code' => '3300', 'name' => 'Retained Earnings', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3301', 'name' => 'Retained Earnings', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3302', 'name' => 'Current Year Earnings', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '3310', 'name' => 'Owner Withdrawals', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 4️⃣ Revenue 4000-4999 =====
            ['code' => '4000', 'name' => 'Revenue', 'parent_code' => '', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            
            // Operating Revenue
            ['code' => '4100', 'name' => 'Operating Revenue', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4101', 'name' => 'Local Sales', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4102', 'name' => 'Export Sales', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4103', 'name' => 'Service Revenue', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4110', 'name' => 'Sales Discounts', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4111', 'name' => 'Sales Returns', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Other Revenue
            ['code' => '4200', 'name' => 'Other Revenue', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4201', 'name' => 'Rental Income', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '4202', 'name' => 'Interest Income', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // ===== 5️⃣ Expenses 5000-5999 =====
            ['code' => '5000', 'name' => 'Expenses', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            
            // Cost of Goods Sold
            ['code' => '5100', 'name' => 'Cost of Goods Sold', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5101', 'name' => 'Purchases', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5102', 'name' => 'Purchase Expenses', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5110', 'name' => 'Purchase Discounts', 'parent_code' => '5100', 'type' => 'expense', 'nature' => 'credit', 'is_group' => false, 'default_currency' => $currency],
            
            // Payroll Expenses
            ['code' => '5200', 'name' => 'Payroll Expenses', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5201', 'name' => 'Basic Salaries', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5202', 'name' => 'Allowances & Bonuses', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5203', 'name' => 'Employer Insurance Contribution', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Operating Expenses
            ['code' => '5300', 'name' => 'Operating Expenses', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5301', 'name' => 'Rent', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5302', 'name' => 'Utilities', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5303', 'name' => 'Communications & Internet', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5304', 'name' => 'Maintenance & Repairs', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5305', 'name' => 'Insurance', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5310', 'name' => 'Office Supplies', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5311', 'name' => 'Transportation', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Depreciation
            ['code' => '5400', 'name' => 'Depreciation Expense', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5401', 'name' => 'Buildings Depreciation', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5402', 'name' => 'Equipment Depreciation', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5403', 'name' => 'Furniture Depreciation', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Financial Expenses
            ['code' => '5500', 'name' => 'Financial Expenses', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5501', 'name' => 'Interest Expense', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5502', 'name' => 'Bank Charges', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            
            // Taxes & Fees
            ['code' => '5600', 'name' => 'Taxes & Fees', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5601', 'name' => 'Income Tax', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
            ['code' => '5602', 'name' => 'VAT Expense', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false, 'default_currency' => $currency],
        ];
    }
}
