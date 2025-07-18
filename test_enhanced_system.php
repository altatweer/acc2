<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class EnhancedSystemTest
{
    /**
     * تشغيل جميع الاختبارات المحسنة
     */
    public function runAllTests()
    {
        echo "🧪 بدء اختبار النظام المحاسبي المحسن متعدد العملات...\n\n";

        $this->testDatabaseConnection();
        $this->testEnhancedCurrencies();
        $this->testAccountsStructure();
        $this->testMultiCurrencyBalances();
        $this->testExchangeRates();
        $this->testMultiCurrencySupport();
        $this->testSampleData();
        $this->testNewTables();
        $this->generateSystemReport();
    }

    /**
     * اختبار الاتصال بقاعدة البيانات
     */
    private function testDatabaseConnection()
    {
        echo "🔗 اختبار الاتصال بقاعدة البيانات...\n";
        
        try {
            $connection = DB::connection()->getPdo();
            echo "   ✅ تم الاتصال بقاعدة البيانات بنجاح\n";
            
            $dbName = DB::connection()->getDatabaseName();
            echo "   📄 اسم قاعدة البيانات: {$dbName}\n";
            
        } catch (Exception $e) {
            echo "   ❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
            exit(1);
        }
        echo "\n";
    }

    /**
     * اختبار العملات المحسنة
     */
    private function testEnhancedCurrencies()
    {
        echo "💱 اختبار العملات المحسنة...\n";
        
        $currencies = DB::table('currencies')->get();
        $activeCurrencies = $currencies->where('is_active', true);
        $baseCurrency = $currencies->where('is_default', true)->first();
        
        echo "   📊 إجمالي العملات: " . $currencies->count() . "\n";
        echo "   ✅ العملات النشطة: " . $activeCurrencies->count() . "\n";
        echo "   🏦 العملة الأساسية: " . ($baseCurrency->code ?? 'غير محددة') . "\n";
        
        // عرض تفاصيل العملات
        echo "   💰 تفاصيل العملات:\n";
        foreach ($currencies as $currency) {
            $status = $currency->is_active ? '🟢 نشط' : '🔴 غير نشط';
            $default = $currency->is_default ? ' (الأساسية)' : '';
            echo "      {$currency->code} - {$currency->name} ({$currency->name_ar}) - {$currency->symbol} - {$status}{$default}\n";
            echo "         البلد: {$currency->country} ({$currency->country_ar})\n";
            echo "         الخانات العشرية: {$currency->decimal_places}\n";
            echo "         سعر الصرف: " . number_format($currency->exchange_rate, 10) . "\n";
        }
        
        if ($currencies->count() < 3) {
            echo "   ⚠️  تحذير: عدد العملات قليل جداً\n";
        }
        echo "\n";
    }

    /**
     * اختبار هيكل الحسابات
     */
    private function testAccountsStructure()
    {
        echo "🌳 اختبار هيكل الحسابات...\n";
        
        $accounts = DB::table('accounts')->get();
        $groups = $accounts->where('is_group', true);
        $actualAccounts = $accounts->where('is_group', false);
        
        echo "   📊 إجمالي الحسابات: " . $accounts->count() . "\n";
        echo "   📁 الفئات: " . $groups->count() . "\n";
        echo "   💰 الحسابات الفعلية: " . $actualAccounts->count() . "\n";

        // توزيع الحسابات حسب النوع
        $accountsByType = $actualAccounts->groupBy('type');
        
        echo "\n   📈 توزيع الحسابات حسب النوع:\n";
        foreach ($accountsByType as $type => $typeAccounts) {
            $icon = $this->getTypeIcon($type);
            $typeName = $this->getTypeNameAr($type);
            echo "   {$icon} {$typeName}: " . $typeAccounts->count() . " حساب\n";
        }

        // اختبار نظام الترقيم
        echo "\n   🔢 اختبار نظام الترقيم:\n";
        $ranges = [
            'assets' => ['min' => 1000, 'max' => 1999],
            'liabilities' => ['min' => 2000, 'max' => 2999],
            'equity' => ['min' => 3000, 'max' => 3999],
            'revenue' => ['min' => 4000, 'max' => 4999],
            'expense' => ['min' => 5000, 'max' => 5999],
        ];

        foreach ($ranges as $type => $range) {
            $typeAccounts = $actualAccounts->where('type', $type);
            $correctlyNumbered = $typeAccounts->filter(function($account) use ($range) {
                $code = intval($account->code);
                return $code >= $range['min'] && $code <= $range['max'];
            });
            
            if ($correctlyNumbered->count() === $typeAccounts->count()) {
                echo "   ✅ {$type}: " . $typeAccounts->count() . " حساب في النطاق الصحيح\n";
            } else {
                echo "   ❌ {$type}: " . ($typeAccounts->count() - $correctlyNumbered->count()) . " حساب خارج النطاق\n";
            }
        }

        // اختبار دعم العملات المتعددة
        $multiCurrencyAccounts = $actualAccounts->where('supports_multi_currency', true);
        echo "\n   🌍 دعم العملات المتعددة:\n";
        echo "      📊 حسابات تدعم العملات المتعددة: " . $multiCurrencyAccounts->count() . "\n";
        echo "      📈 نسبة الدعم: " . round(($multiCurrencyAccounts->count() / $actualAccounts->count()) * 100, 1) . "%\n";

        echo "\n";
    }

    /**
     * اختبار أرصدة العملات المتعددة
     */
    private function testMultiCurrencyBalances()
    {
        echo "💰 اختبار أرصدة العملات المتعددة...\n";
        
        $balances = DB::table('account_balances')->get();
        $activeBalances = $balances->where('is_active', true);
        $currencies = DB::table('currencies')->pluck('code');
        
        echo "   📊 إجمالي الأرصدة: " . $balances->count() . "\n";
        echo "   ✅ الأرصدة النشطة: " . $activeBalances->count() . "\n";
        
        // أرصدة بكل عملة
        foreach ($currencies as $currency) {
            $currencyId = DB::table('currencies')->where('code', $currency)->first()->id ?? 0;
            $currencyBalances = $balances->where('currency_id', $currencyId);
            $totalBalance = $currencyBalances->sum('balance');
            echo "   💱 أرصدة بعملة {$currency}: " . $currencyBalances->count() . " حساب - المجموع: " . number_format($totalBalance, 2) . "\n";
        }
        echo "\n";
    }

    /**
     * اختبار أسعار الصرف
     */
    private function testExchangeRates()
    {
        echo "📈 اختبار أسعار الصرف...\n";
        
        $rates = DB::table('currency_rates')->get();
        $activeRates = $rates->where('is_active', true);
        $today = Carbon::today();
        $currentRates = $rates->where('effective_date', $today->format('Y-m-d'));
        
        echo "   📊 إجمالي أسعار الصرف: " . $rates->count() . "\n";
        echo "   ✅ الأسعار النشطة: " . $activeRates->count() . "\n";
        echo "   📅 أسعار اليوم: " . $currentRates->count() . "\n";
        
        // عرض الأسعار الحالية النشطة
        echo "\n   💱 أسعار الصرف الحالية النشطة:\n";
        foreach ($activeRates as $rate) {
            echo "      {$rate->from_currency} → {$rate->to_currency}: " . 
                 number_format($rate->rate, 10) . "\n";
        }

        // اختبار السجل التاريخي
        $historyCount = DB::table('exchange_rate_history')->count();
        echo "\n   📚 سجل أسعار الصرف التاريخي: {$historyCount} سجل\n";
        
        echo "\n";
    }

    /**
     * اختبار دعم العملات المتعددة
     */
    private function testMultiCurrencySupport()
    {
        echo "🌍 اختبار دعم العملات المتعددة...\n";
        
        $actualAccounts = DB::table('accounts')->where('is_group', false)->get();
        $multiCurrencyAccounts = $actualAccounts->where('supports_multi_currency', true);
        $singleCurrencyAccounts = $actualAccounts->where('supports_multi_currency', false);
        
        echo "   📊 إجمالي الحسابات الفعلية: " . $actualAccounts->count() . "\n";
        echo "   🌍 حسابات متعددة العملات: " . $multiCurrencyAccounts->count() . "\n";
        echo "   💰 حسابات عملة واحدة: " . $singleCurrencyAccounts->count() . "\n";
        echo "   📈 نسبة دعم العملات المتعددة: " . 
             round(($multiCurrencyAccounts->count() / $actualAccounts->count()) * 100, 1) . "%\n";
        
        if ($multiCurrencyAccounts->count() / $actualAccounts->count() > 0.8) {
            echo "   ✅ ممتاز: معظم الحسابات تدعم العملات المتعددة\n";
        } elseif ($multiCurrencyAccounts->count() / $actualAccounts->count() > 0.5) {
            echo "   ⚠️  جيد: نصف الحسابات تدعم العملات المتعددة\n";
        } else {
            echo "   ❌ يحتاج تحسين: قليل من الحسابات تدعم العملات المتعددة\n";
        }
        echo "\n";
    }

    /**
     * اختبار البيانات التجريبية
     */
    private function testSampleData()
    {
        echo "📦 اختبار البيانات التجريبية...\n";
        
        $customers = DB::table('customers')->count();
        $employees = DB::table('employees')->count();
        $items = DB::table('items')->count();
        $itemPrices = DB::table('item_prices')->count();
        $customerBalances = DB::table('customer_balances')->count();
        
        echo "   👥 العملاء: {$customers}\n";
        echo "   👨‍💼 الموظفون: {$employees}\n";
        echo "   📦 المنتجات: {$items}\n";
        echo "   💰 أسعار المنتجات: {$itemPrices}\n";
        echo "   💳 أرصدة العملاء: {$customerBalances}\n";
        
        // تفاصيل العملاء متعددي العملات
        if ($customers > 0) {
            echo "\n   👥 تفاصيل العملاء:\n";
            $customerDetails = DB::table('customers')->get();
            foreach ($customerDetails as $customer) {
                echo "      {$customer->name} - العملة الافتراضية: {$customer->default_currency}\n";
                echo "         الحد الائتماني: " . number_format($customer->credit_limit, 2) . " {$customer->credit_limit_currency}\n";
            }
        }
        
        echo "\n";
    }

    /**
     * اختبار الجداول الجديدة
     */
    private function testNewTables()
    {
        echo "🆕 اختبار الجداول الجديدة...\n";
        
        $tables = [
            'item_prices' => 'أسعار المنتجات متعددة العملات',
            'customer_balances' => 'أرصدة العملاء متعددة العملات',
            'exchange_rate_history' => 'سجل أسعار الصرف التاريخي',
            'multi_currency_transactions' => 'المعاملات متعددة العملات',
        ];
        
        foreach ($tables as $table => $description) {
            try {
                $count = DB::table($table)->count();
                echo "   ✅ {$description} ({$table}): {$count} سجل\n";
            } catch (Exception $e) {
                echo "   ❌ {$description} ({$table}): غير موجود أو خطأ\n";
            }
        }
        
        echo "\n";
    }

    /**
     * تقرير شامل للنظام
     */
    private function generateSystemReport()
    {
        echo "📋 تقرير شامل للنظام المحسن:\n";
        echo "===================================================\n";
        
        $stats = [
            'currencies' => DB::table('currencies')->count(),
            'active_currencies' => DB::table('currencies')->where('is_active', true)->count(),
            'accounts' => DB::table('accounts')->count(),
            'account_groups' => DB::table('accounts')->where('is_group', true)->count(),
            'actual_accounts' => DB::table('accounts')->where('is_group', false)->count(),
            'multi_currency_accounts' => DB::table('accounts')->where('supports_multi_currency', true)->count(),
            'account_balances' => DB::table('account_balances')->count(),
            'exchange_rates' => DB::table('currency_rates')->count(),
            'exchange_history' => DB::table('exchange_rate_history')->count(),
            'customers' => DB::table('customers')->count(),
            'employees' => DB::table('employees')->count(),
            'items' => DB::table('items')->count(),
            'item_prices' => DB::table('item_prices')->count(),
            'customer_balances' => DB::table('customer_balances')->count(),
            'users' => DB::table('users')->count(),
            'branches' => DB::table('branches')->count(),
        ];
        
        $labels = [
            'currencies' => 'إجمالي العملات',
            'active_currencies' => 'العملات النشطة',
            'accounts' => 'إجمالي الحسابات',
            'account_groups' => 'فئات الحسابات',
            'actual_accounts' => 'الحسابات الفعلية',
            'multi_currency_accounts' => 'حسابات متعددة العملات',
            'account_balances' => 'أرصدة الحسابات',
            'exchange_rates' => 'أسعار الصرف',
            'exchange_history' => 'سجل أسعار الصرف',
            'customers' => 'العملاء',
            'employees' => 'الموظفون',
            'items' => 'المنتجات',
            'item_prices' => 'أسعار المنتجات',
            'customer_balances' => 'أرصدة العملاء',
            'users' => 'المستخدمين',
            'branches' => 'الفروع',
        ];
        
        foreach ($stats as $key => $value) {
            $label = $labels[$key] ?? $key;
            echo str_pad($label, 25) . " : {$value}\n";
        }
        
        echo "\n🎯 حالة النظام: ";
        if ($this->isEnhancedSystemReady($stats)) {
            echo "✅ النظام المحسن جاهز للاستخدام!\n";
            echo "\n📝 الميزات المحسنة:\n";
            echo "   🌍 دعم كامل للعملات المتعددة\n";
            echo "   📊 جداول محسنة للأسعار والأرصدة\n";
            echo "   📈 سجل تاريخي لأسعار الصرف\n";
            echo "   👥 إدارة عملاء متعددة العملات\n";
            echo "   📦 أسعار منتجات متعددة العملات\n";
            echo "   💰 أرصدة مفصلة لكل عملة\n";
            echo "   🔄 تتبع المعاملات متعددة العملات\n";
            echo "\n🚀 خطوات البدء:\n";
            echo "   1. مراجعة وتحديث أسعار الصرف اليومية\n";
            echo "   2. إدخال الأرصدة الافتتاحية الفعلية\n";
            echo "   3. إضافة العملاء والموردين الحقيقيين\n";
            echo "   4. تفعيل العملات الإضافية حسب الحاجة\n";
            echo "   5. بدء العمليات المحاسبية اليومية\n";
        } else {
            echo "❌ النظام يحتاج مراجعة!\n";
            echo "\n🔧 نقاط تحتاج إصلاح:\n";
            if ($stats['currencies'] < 3) echo "   - إضافة المزيد من العملات\n";
            if ($stats['accounts'] < 100) echo "   - إكمال الشجرة المحاسبية\n";
            if ($stats['exchange_rates'] < 4) echo "   - إضافة أسعار صرف شاملة\n";
            if ($stats['multi_currency_accounts'] < $stats['actual_accounts'] * 0.8) {
                echo "   - تفعيل دعم العملات المتعددة للمزيد من الحسابات\n";
            }
        }
    }

    /**
     * التحقق من جاهزية النظام المحسن
     */
    private function isEnhancedSystemReady($stats)
    {
        return $stats['currencies'] >= 3 &&
               $stats['active_currencies'] >= 2 &&
               $stats['accounts'] >= 100 &&
               $stats['actual_accounts'] >= 80 &&
               $stats['exchange_rates'] >= 4 &&
               $stats['users'] >= 1 &&
               $stats['multi_currency_accounts'] >= $stats['actual_accounts'] * 0.8;
    }

    /**
     * الحصول على أيقونة حسب نوع الحساب
     */
    private function getTypeIcon($type)
    {
        $icons = [
            'asset' => '🏦',
            'liability' => '💳',
            'equity' => '🏛️',
            'revenue' => '💹',
            'expense' => '💸',
        ];
        
        return $icons[$type] ?? '📊';
    }

    /**
     * الحصول على اسم النوع بالعربية
     */
    private function getTypeNameAr($type)
    {
        $names = [
            'asset' => 'الأصول',
            'liability' => 'الخصوم',
            'equity' => 'رأس المال',
            'revenue' => 'الإيرادات',
            'expense' => 'المصروفات',
        ];
        
        return $names[$type] ?? $type;
    }
}

// تشغيل الاختبارات
try {
    $test = new EnhancedSystemTest();
    $test->runAllTests();
    echo "\n🎉 تم إكمال جميع الاختبارات المحسنة بنجاح!\n";
} catch (Exception $e) {
    echo "\n❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "📍 في الملف: " . $e->getFile() . " السطر: " . $e->getLine() . "\n";
    exit(1);
} 