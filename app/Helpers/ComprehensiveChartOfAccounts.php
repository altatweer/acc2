<?php

namespace App\Helpers;

class ComprehensiveChartOfAccounts
{
    /**
     * الحصول على الشجرة المحاسبية الشاملة باللغة العربية (نسخة مطابقة للنظام المحلي 100%)
     */
    public static function getArabicChart($currency)
    {
        $accounts = [
            // ===== 1️⃣ الأصول (Assets) 1000-1999 =====
            
            // الأصول المتداولة (1000-1499)
            ['code' => '1000', 'name' => 'الأصول المتداولة', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            
            // النقدية والبنوك (1100-1199)
            ['code' => '1100', 'name' => 'النقدية والبنوك', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1101', 'name' => 'الصندوق الرئيسي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true],
            ['code' => '1102', 'name' => 'صندوق المبيعات', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true],
            ['code' => '1103', 'name' => 'صندوق المصروفات الصغيرة', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false, 'is_cash_box' => true],
            ['code' => '1110', 'name' => 'البنك المركزي العراقي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1111', 'name' => 'بنك بغداد', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1112', 'name' => 'البنك التجاري العراقي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1113', 'name' => 'البنك الأهلي', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1114', 'name' => 'بنوك دولية', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1120', 'name' => 'ودائع قصيرة الأجل', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1130', 'name' => 'استثمارات سائلة', 'parent_code' => '1100', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            
            // العملاء والذمم المدينة (1200-1299)
            ['code' => '1200', 'name' => 'العملاء والذمم المدينة', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1201', 'name' => 'العملاء المحليون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1202', 'name' => 'العملاء الدوليون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1203', 'name' => 'العملاء الحكوميون', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1210', 'name' => 'أوراق القبض', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1211', 'name' => 'شيكات برسم التحصيل', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1220', 'name' => 'مدفوعات مقدمة للموردين', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1221', 'name' => 'إيرادات مقبوضة مقدماً', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1222', 'name' => 'تأمينات مدفوعة مقدماً', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1230', 'name' => 'سلف الموظفين', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1231', 'name' => 'عهد الموظفين', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1250', 'name' => 'مخصص الديون المشكوك فيها', 'parent_code' => '1200', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // المخزون والبضائع (1300-1399)
            ['code' => '1300', 'name' => 'المخزون والبضائع', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1301', 'name' => 'بضاعة جاهزة للبيع', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1302', 'name' => 'بضاعة تحت التشغيل', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1303', 'name' => 'مواد خام ومستلزمات', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1310', 'name' => 'لوازم مكتبية', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1311', 'name' => 'لوازم صيانة وتشغيل', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1320', 'name' => 'بضاعة في الطريق', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1321', 'name' => 'بضاعة أمانة', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1390', 'name' => 'مخصص تقادم المخزون', 'parent_code' => '1300', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // أصول متداولة أخرى (1400-1499)
            ['code' => '1400', 'name' => 'أصول متداولة أخرى', 'parent_code' => '1000', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1401', 'name' => 'ودائع ضمان', 'parent_code' => '1400', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1402', 'name' => 'مصروفات مدفوعة مقدماً', 'parent_code' => '1400', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1403', 'name' => 'أرصدة مدينة متنوعة', 'parent_code' => '1400', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1490', 'name' => 'أصول متداولة أخرى', 'parent_code' => '1400', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            
            // الأصول الثابتة (1500-1999)
            ['code' => '1500', 'name' => 'الأصول الثابتة', 'parent_code' => '', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            
            // الأراضي والمباني (1510-1599)
            ['code' => '1510', 'name' => 'الأراضي والمباني', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1501', 'name' => 'الأراضي', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1511', 'name' => 'المباني الإدارية', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1512', 'name' => 'المباني الإنتاجية', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1513', 'name' => 'المباني التجارية', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1514', 'name' => 'المستودعات والمخازن', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1520', 'name' => 'التحسينات والإضافات', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1590', 'name' => 'مجمع إهلاك المباني', 'parent_code' => '1510', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // الأثاث والمعدات (1600-1699)
            ['code' => '1600', 'name' => 'الأثاث والمعدات', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1601', 'name' => 'أثاث المكاتب', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1602', 'name' => 'أجهزة الكمبيوتر', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1603', 'name' => 'أجهزة الشبكات والخوادم', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1604', 'name' => 'أجهزة الطباعة والمسح', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1610', 'name' => 'معدات التشغيل والإنتاج', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1611', 'name' => 'أدوات ومعدات يدوية', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1612', 'name' => 'معدات الأمان والسلامة', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1620', 'name' => 'الأجهزة المكتبية الأخرى', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1690', 'name' => 'مجمع إهلاك الأثاث والمعدات', 'parent_code' => '1600', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // وسائل النقل (1700-1799)
            ['code' => '1700', 'name' => 'وسائل النقل', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1701', 'name' => 'سيارات الإدارة', 'parent_code' => '1700', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1702', 'name' => 'سيارات النقل والتوصيل', 'parent_code' => '1700', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1703', 'name' => 'دراجات نارية', 'parent_code' => '1700', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1704', 'name' => 'معدات النقل الثقيل', 'parent_code' => '1700', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1790', 'name' => 'مجمع إهلاك وسائل النقل', 'parent_code' => '1700', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // الأصول غير الملموسة (1800-1899)
            ['code' => '1800', 'name' => 'الأصول غير الملموسة', 'parent_code' => '1500', 'type' => 'asset', 'nature' => 'debit', 'is_group' => true],
            ['code' => '1801', 'name' => 'برمجيات وتراخيص', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1802', 'name' => 'العلامات التجارية', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1803', 'name' => 'براءات الاختراع', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1804', 'name' => 'الشهرة التجارية', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1810', 'name' => 'مصروفات التأسيس', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1811', 'name' => 'مصروفات ما قبل التشغيل', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'debit', 'is_group' => false],
            ['code' => '1890', 'name' => 'مجمع إطفاء الأصول غير الملموسة', 'parent_code' => '1800', 'type' => 'asset', 'nature' => 'credit', 'is_group' => false],
            
            // ===== 2️⃣ الخصوم (Liabilities) 2000-2999 =====
            
            // الخصوم المتداولة (2000-2499)
            ['code' => '2000', 'name' => 'الخصوم المتداولة', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            
            // الموردون والدائنون (2100-2199)
            ['code' => '2100', 'name' => 'الموردون والدائنون', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2101', 'name' => 'الموردون المحليون', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2102', 'name' => 'الموردون الدوليون', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2103', 'name' => 'موردو الخدمات', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2110', 'name' => 'أوراق الدفع', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2111', 'name' => 'شيكات برسم الدفع', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2120', 'name' => 'دفعات مقدمة من العملاء', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2121', 'name' => 'إيرادات مقبوضة مقدماً', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2130', 'name' => 'حسابات دائنة متنوعة', 'parent_code' => '2100', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            
            // المستحقات والالتزامات (2200-2299)
            ['code' => '2200', 'name' => 'المستحقات والالتزامات', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2201', 'name' => 'رواتب وأجور مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2202', 'name' => 'مكافآت نهاية الخدمة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2203', 'name' => 'إجازات مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2204', 'name' => 'حوافز ومكافآت مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2210', 'name' => 'ضرائب مستحقة الدفع', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2211', 'name' => 'ضريبة القيمة المضافة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2212', 'name' => 'ضريبة الدخل', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2220', 'name' => 'مصروفات مستحقة الدفع', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2221', 'name' => 'فوائد مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2222', 'name' => 'إيجارات مستحقة', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2230', 'name' => 'التزامات أخرى', 'parent_code' => '2200', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            
            // خصومات الموظفين (2300-2399)
            ['code' => '2300', 'name' => 'خصومات الموظفين', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2301', 'name' => 'سلف الموظفين', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2302', 'name' => 'خصم التأمين الصحي', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2303', 'name' => 'خصم التأمين الاجتماعي', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2304', 'name' => 'خصم صندوق التقاعد', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2310', 'name' => 'خصم القروض الشخصية', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2311', 'name' => 'خصم الضرائب الشخصية', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2390', 'name' => 'خصومات أخرى', 'parent_code' => '2300', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            
            // قروض قصيرة الأجل (2400-2499)
            ['code' => '2400', 'name' => 'قروض قصيرة الأجل', 'parent_code' => '2000', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2401', 'name' => 'قروض بنكية قصيرة الأجل', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2402', 'name' => 'تسهيلات ائتمانية', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2403', 'name' => 'قروض من الشركاء', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2404', 'name' => 'قروض شخصية', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2490', 'name' => 'قروض قصيرة أخرى', 'parent_code' => '2400', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            
            // الخصوم طويلة الأجل (2500-2999)
            ['code' => '2500', 'name' => 'الخصوم طويلة الأجل', 'parent_code' => '', 'type' => 'liability', 'nature' => 'credit', 'is_group' => true],
            ['code' => '2501', 'name' => 'قروض بنكية طويلة الأجل', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2502', 'name' => 'قروض الاستثمار والتطوير', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2503', 'name' => 'سندات مصدرة', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2510', 'name' => 'قروض من الشركاء', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2511', 'name' => 'قروض شخصية طويلة الأجل', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2520', 'name' => 'مخصص نهاية الخدمة', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2521', 'name' => 'مخصص التزامات عمالية', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            ['code' => '2590', 'name' => 'التزامات طويلة أخرى', 'parent_code' => '2500', 'type' => 'liability', 'nature' => 'credit', 'is_group' => false],
            
            // ===== 3️⃣ حقوق الملكية (Equity) 3000-3999 =====
            
            ['code' => '3000', 'name' => 'حقوق الملكية', 'parent_code' => '', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            
            // رأس المال
            ['code' => '3100', 'name' => 'رأس المال', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3101', 'name' => 'رأس المال المدفوع', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3102', 'name' => 'رأس المال المصرح به', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3103', 'name' => 'علاوة إصدار رأس المال', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3110', 'name' => 'تغيرات رأس المال', 'parent_code' => '3100', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            
            // الاحتياطيات
            ['code' => '3200', 'name' => 'الاحتياطيات', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3201', 'name' => 'الاحتياطي القانوني', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3202', 'name' => 'احتياطي إعادة التقييم', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3203', 'name' => 'احتياطي تقلبات العملة', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3204', 'name' => 'احتياطي الطوارئ', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3290', 'name' => 'احتياطيات أخرى', 'parent_code' => '3200', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            
            // الأرباح المحتجزة
            ['code' => '3300', 'name' => 'الأرباح المحتجزة', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'credit', 'is_group' => true],
            ['code' => '3301', 'name' => 'أرباح مرحلة من سنوات سابقة', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3302', 'name' => 'ربح العام الحالي', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            ['code' => '3303', 'name' => 'خسارة العام الحالي', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false],
            ['code' => '3310', 'name' => 'أرباح أو خسائر متراكمة', 'parent_code' => '3300', 'type' => 'equity', 'nature' => 'credit', 'is_group' => false],
            
            // المسحوبات والتوزيعات
            ['code' => '3400', 'name' => 'المسحوبات والتوزيعات', 'parent_code' => '3000', 'type' => 'equity', 'nature' => 'debit', 'is_group' => true],
            ['code' => '3401', 'name' => 'مسحوبات شخصية للشركاء', 'parent_code' => '3400', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false],
            ['code' => '3402', 'name' => 'توزيعات أرباح معلنة', 'parent_code' => '3400', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false],
            ['code' => '3403', 'name' => 'توزيعات أرباح مدفوعة', 'parent_code' => '3400', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false],
            ['code' => '3490', 'name' => 'مسحوبات وتوزيعات أخرى', 'parent_code' => '3400', 'type' => 'equity', 'nature' => 'debit', 'is_group' => false],
            
            // ===== 4️⃣ الإيرادات (Revenues) 4000-4999 =====
            
            ['code' => '4000', 'name' => 'الإيرادات', 'parent_code' => '', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            
            // إيرادات المبيعات (4100-4199)
            ['code' => '4100', 'name' => 'إيرادات المبيعات', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4101', 'name' => 'مبيعات محلية', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4102', 'name' => 'مبيعات تصدير', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4103', 'name' => 'مبيعات جملة', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4104', 'name' => 'مبيعات مفرق', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4105', 'name' => 'مبيعات إلكترونية', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4110', 'name' => 'خصم مسموح به', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false],
            ['code' => '4111', 'name' => 'خصم كمية', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false],
            ['code' => '4120', 'name' => 'مردودات المبيعات', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false],
            ['code' => '4130', 'name' => 'مسموحات المبيعات', 'parent_code' => '4100', 'type' => 'revenue', 'nature' => 'debit', 'is_group' => false],
            
            // إيرادات الخدمات (4200-4299)
            ['code' => '4200', 'name' => 'إيرادات الخدمات', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4201', 'name' => 'خدمات استشارية', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4202', 'name' => 'خدمات صيانة', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4203', 'name' => 'خدمات تدريب', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4204', 'name' => 'خدمات تقنية', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4205', 'name' => 'خدمات نقل وتوصيل', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4206', 'name' => 'خدمات تأجير', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4210', 'name' => 'عمولات وسمسرة', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4290', 'name' => 'خدمات أخرى', 'parent_code' => '4200', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            
            // إيرادات تشغيلية أخرى (4300-4499)
            ['code' => '4300', 'name' => 'إيرادات تشغيلية أخرى', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4301', 'name' => 'إيرادات الإيجار', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4302', 'name' => 'إيرادات الاستثمار', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4303', 'name' => 'أرباح بيع أصول', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4304', 'name' => 'أرباح أسعار الصرف', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4305', 'name' => 'إيرادات فوائد', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4310', 'name' => 'مبالغ مستردة', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4311', 'name' => 'تخفيض مخصصات', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4390', 'name' => 'إيرادات متنوعة', 'parent_code' => '4300', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            
            // إيرادات استثنائية (4400-4499)
            ['code' => '4400', 'name' => 'إيرادات استثنائية', 'parent_code' => '4000', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => true],
            ['code' => '4401', 'name' => 'إيرادات بيع استثمارات', 'parent_code' => '4400', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4402', 'name' => 'تعويضات مقبوضة', 'parent_code' => '4400', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4403', 'name' => 'أرباح عقود منتهية', 'parent_code' => '4400', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4404', 'name' => 'إيرادات إعادة تقييم', 'parent_code' => '4400', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            ['code' => '4490', 'name' => 'إيرادات استثنائية أخرى', 'parent_code' => '4400', 'type' => 'revenue', 'nature' => 'credit', 'is_group' => false],
            
            // ===== 5️⃣ المصروفات (Expenses) 5000-5999 =====
            
            // تكلفة المبيعات (5000-5099)
            ['code' => '5000', 'name' => 'تكلفة المبيعات', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5001', 'name' => 'تكلفة البضاعة المباعة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5002', 'name' => 'تكلفة المواد المباشرة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5003', 'name' => 'تكلفة العمالة المباشرة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5004', 'name' => 'تكاليف التصنيع العامة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5010', 'name' => 'مصروفات الشراء والاستيراد', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5011', 'name' => 'رسوم جمركية ونقل', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5012', 'name' => 'تأمين البضائع', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5020', 'name' => 'خصم مكتسب', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'credit', 'is_group' => false],
            ['code' => '5090', 'name' => 'تكاليف مباشرة أخرى', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // المشتريات (5100-5109)
            ['code' => '5101', 'name' => 'مشتريات البضاعة', 'parent_code' => '5000', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // مصروفات الموظفين (5200-5299)
            ['code' => '5200', 'name' => 'مصروفات الموظفين', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5201', 'name' => 'الرواتب الأساسية', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5202', 'name' => 'البدلات والعلاوات', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5203', 'name' => 'المكافآت والحوافز', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5204', 'name' => 'مكافآت نهاية الخدمة', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5205', 'name' => 'بدل إجازات', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5206', 'name' => 'ساعات إضافية', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5210', 'name' => 'تأمينات اجتماعية', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5211', 'name' => 'تأمين صحي', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5212', 'name' => 'صندوق التقاعد', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5220', 'name' => 'تدريب وتطوير الموظفين', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5221', 'name' => 'مصروفات التوظيف والاختيار', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5230', 'name' => 'مصروفات سفر الموظفين', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5231', 'name' => 'بدل مواصلات', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5232', 'name' => 'بدل سكن وإقامة', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5240', 'name' => 'زي رسمي ومعدات عمل', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5241', 'name' => 'وجبات ومرطبات', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5290', 'name' => 'مصروفات موظفين أخرى', 'parent_code' => '5200', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // المصروفات الإدارية (5300-5399)
            ['code' => '5300', 'name' => 'المصروفات الإدارية', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5301', 'name' => 'إيجار المكاتب والمباني', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5302', 'name' => 'كهرباء وماء وغاز', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5303', 'name' => 'هاتف وإنترنت واتصالات', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5304', 'name' => 'خدمات النظافة والأمن', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5305', 'name' => 'صيانة وإصلاح المباني', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5310', 'name' => 'لوازم مكتبية وقرطاسية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5311', 'name' => 'طباعة ونسخ', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5312', 'name' => 'بريد وشحن', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5320', 'name' => 'خدمات محاسبية وقانونية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5321', 'name' => 'خدمات استشارية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5322', 'name' => 'رسوم وتراخيص حكومية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5323', 'name' => 'رسوم بنكية ومالية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5330', 'name' => 'تأمين عام', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5331', 'name' => 'ضرائب ورسوم', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5340', 'name' => 'مصروفات سفر إدارية', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5341', 'name' => 'اجتماعات وضيافة', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5350', 'name' => 'اشتراكات ومجلات', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5351', 'name' => 'برمجيات وتراخيص', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5390', 'name' => 'مصروفات إدارية أخرى', 'parent_code' => '5300', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // مصروفات التسويق والمبيعات (5400-5499)
            ['code' => '5400', 'name' => 'مصروفات التسويق والمبيعات', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5401', 'name' => 'إعلان وتسويق', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5402', 'name' => 'مصروفات المعارض والفعاليات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5403', 'name' => 'عمولات المبيعات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5404', 'name' => 'حوافز فريق المبيعات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5405', 'name' => 'هدايا ودعاية', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5410', 'name' => 'مصروفات سفر المبيعات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5411', 'name' => 'مصروفات العملاء', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5420', 'name' => 'تطوير المنتجات', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5421', 'name' => 'أبحاث السوق', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5430', 'name' => 'مطبوعات تسويقية', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5431', 'name' => 'تصميم وإبداع', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5440', 'name' => 'موقع إلكتروني وتسويق رقمي', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5441', 'name' => 'وسائل التواصل الاجتماعي', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5490', 'name' => 'مصروفات تسويق أخرى', 'parent_code' => '5400', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // مصروفات النقل والمواصلات (5500-5599)
            ['code' => '5500', 'name' => 'مصروفات النقل والمواصلات', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5501', 'name' => 'وقود ومحروقات', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5502', 'name' => 'صيانة وإصلاح المركبات', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5503', 'name' => 'تأمين المركبات', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5504', 'name' => 'رسوم ترخيص ومرور', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5505', 'name' => 'قطع غيار', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5510', 'name' => 'أجور نقل وتوصيل', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5511', 'name' => 'شحن دولي ومحلي', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5520', 'name' => 'مواقف وطرق', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5521', 'name' => 'مخالفات مرورية', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5590', 'name' => 'مصروفات نقل أخرى', 'parent_code' => '5500', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // مصروفات الصيانة والتشغيل (5600-5699)
            ['code' => '5600', 'name' => 'مصروفات الصيانة والتشغيل', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5601', 'name' => 'صيانة الأثاث والمعدات', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5602', 'name' => 'صيانة أجهزة الكمبيوتر', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5603', 'name' => 'صيانة الشبكات والأنظمة', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5604', 'name' => 'صيانة أجهزة المكاتب', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5610', 'name' => 'قطع غيار ومستلزمات', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5611', 'name' => 'أدوات ومواد صيانة', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5620', 'name' => 'عقود صيانة خارجية', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5621', 'name' => 'دعم تقني', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5690', 'name' => 'مصروفات صيانة أخرى', 'parent_code' => '5600', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // المصروفات المالية (5700-5799)
            ['code' => '5700', 'name' => 'المصروفات المالية', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5701', 'name' => 'فوائد القروض البنكية', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5702', 'name' => 'فوائد القروض الشخصية', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5703', 'name' => 'رسوم بنكية ومالية', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5704', 'name' => 'رسوم تحويلات', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5705', 'name' => 'خسائر أسعار الصرف', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5710', 'name' => 'خسائر بيع أصول', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5711', 'name' => 'خسائر استثمارات', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5720', 'name' => 'مخصص الديون المعدومة', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5721', 'name' => 'شطب ديون', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5790', 'name' => 'مصروفات مالية أخرى', 'parent_code' => '5700', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // الإهلاك والإطفاء (5800-5899)
            ['code' => '5800', 'name' => 'مصروفات الإهلاك والإطفاء', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5801', 'name' => 'إهلاك المباني', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5802', 'name' => 'إهلاك الأثاث والمعدات', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5803', 'name' => 'إهلاك أجهزة الكمبيوتر', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5804', 'name' => 'إهلاك وسائل النقل', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5805', 'name' => 'إهلاك المعدات التقنية', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5810', 'name' => 'إطفاء البرمجيات', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5811', 'name' => 'إطفاء الأصول غير الملموسة', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5812', 'name' => 'إطفاء مصروفات التأسيس', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5890', 'name' => 'إهلاك وإطفاء أخرى', 'parent_code' => '5800', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            
            // مصروفات أخرى (5900-5999)
            ['code' => '5900', 'name' => 'مصروفات أخرى', 'parent_code' => '', 'type' => 'expense', 'nature' => 'debit', 'is_group' => true],
            ['code' => '5901', 'name' => 'غرامات وجزاءات', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5902', 'name' => 'تبرعات ومساعدات', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5903', 'name' => 'خسائر طبيعية واستثنائية', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5904', 'name' => 'مصروفات قضائية وقانونية', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5910', 'name' => 'ضرائب ورسوم إضافية', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5911', 'name' => 'غرامات ضريبية', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5920', 'name' => 'مصروفات إعادة هيكلة', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5921', 'name' => 'تعويضات مدفوعة', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5930', 'name' => 'مصروفات استثنائية', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
            ['code' => '5990', 'name' => 'مصروفات متنوعة', 'parent_code' => '5900', 'type' => 'expense', 'nature' => 'debit', 'is_group' => false],
        ];

        // إضافة default_currency لجميع الحسابات
        return self::addCurrencyToAccounts($accounts, $currency);
    }

    /**
     * إضافة العملة الافتراضية لجميع الحسابات
     */
    private static function addCurrencyToAccounts($accounts, $currency)
    {
        return array_map(function($account) use ($currency) {
            if (!$account['is_group']) {
                $account['default_currency'] = $currency;
            }
            return $account;
        }, $accounts);
    }

    /**
     * الحصول على الشجرة المحاسبية الشاملة باللغة الإنجليزية
     */
    public static function getEnglishChart($currency)
    {
        // مؤقتاً، استخدم نفس الشجرة العربية
        // يمكن تطوير النسخة الإنجليزية لاحقاً
        return self::getArabicChart($currency);
    }
}
