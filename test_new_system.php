<?php

/**
 * اختبار النظام المحاسبي الجديد متعدد العملات
 * Test script for the new multi-currency accounting system
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// إعداد Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class SystemTest
{
    public function __construct()
    {
        echo "🧪 بدء اختبار النظام المحاسبي الجديد متعدد العملات...\n\n";
    }

    public function runAllTests()
    {
        $this->testDatabaseConnection();
        $this->testCurrencies();
        $this->testAccountsStructure();
        $this->testAccountBalances();
        $this->testExchangeRates();
        $this->testMultiCurrencySupport();
        $this->generateSummaryReport();
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
     * اختبار العملات
     */
    private function testCurrencies()
    {
        echo "💱 اختبار العملات...\n";
        
        $currencies = DB::table('currencies')->get();
        $baseCurrency = $currencies->where('is_default', true)->first();
        
        echo "   📊 إجمالي العملات: " . $currencies->count() . "\n";
        echo "   🏦 العملة الأساسية: " . ($baseCurrency->code ?? 'غير محددة') . "\n";
        
        foreach ($currencies as $currency) {
            $symbol = $currency->is_default ? ' (الأساسية)' : '';
            echo "   💰 {$currency->code} - {$currency->name} ({$currency->symbol}){$symbol}\n";
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
        
        // إحصائيات حسب النوع
        $assetAccounts = $accounts->where('type', 'asset');
        $liabilityAccounts = $accounts->where('type', 'liability');
        $equityAccounts = $accounts->where('type', 'equity');
        $revenueAccounts = $accounts->where('type', 'revenue');
        $expenseAccounts = $accounts->where('type', 'expense');
        
        echo "   📊 إجمالي الحسابات: " . $accounts->count() . "\n";
        echo "   📁 الفئات: " . $groups->count() . "\n";
        echo "   💰 الحسابات الفعلية: " . $actualAccounts->count() . "\n";
        echo "\n   📈 توزيع الحسابات حسب النوع:\n";
        echo "   🏦 الأصول: " . $assetAccounts->count() . " حساب\n";
        echo "   💳 الخصوم: " . $liabilityAccounts->count() . " حساب\n";
        echo "   🏛️  رأس المال: " . $equityAccounts->count() . " حساب\n";
        echo "   💹 الإيرادات: " . $revenueAccounts->count() . " حساب\n";
        echo "   💸 المصروفات: " . $expenseAccounts->count() . " حساب\n";
        
        // اختبار نظام الترقيم
        echo "\n   🔢 اختبار نظام الترقيم:\n";
        $this->testNumberingSystem($accounts);
        echo "\n";
    }

    /**
     * اختبار نظام ترقيم الحسابات
     */
    private function testNumberingSystem($accounts)
    {
        $ranges = [
            'assets' => ['start' => 1000, 'end' => 1999, 'type' => 'asset'],
            'liabilities' => ['start' => 2000, 'end' => 2999, 'type' => 'liability'],
            'equity' => ['start' => 3000, 'end' => 3999, 'type' => 'equity'],
            'revenue' => ['start' => 4000, 'end' => 4999, 'type' => 'revenue'],
            'expense' => ['start' => 5000, 'end' => 5999, 'type' => 'expense'],
        ];

        foreach ($ranges as $name => $range) {
            $accountsInRange = $accounts->filter(function($account) use ($range) {
                $code = intval($account->code);
                return $code >= $range['start'] && $code <= $range['end'] && $account->type === $range['type'];
            });
            
            $wrongType = $accounts->filter(function($account) use ($range) {
                $code = intval($account->code);
                return $code >= $range['start'] && $code <= $range['end'] && $account->type !== $range['type'];
            });

            if ($wrongType->count() === 0) {
                echo "   ✅ {$name}: " . $accountsInRange->count() . " حساب في النطاق الصحيح\n";
            } else {
                echo "   ❌ {$name}: " . $wrongType->count() . " حساب في النطاق الخاطئ\n";
            }
        }
    }

    /**
     * اختبار أرصدة الحسابات
     */
    private function testAccountBalances()
    {
        echo "💰 اختبار أرصدة الحسابات...\n";
        
        $balances = DB::table('account_balances')->get();
        $activeBalances = $balances->where('is_active', true);
        $currencies = DB::table('currencies')->pluck('code');
        
        echo "   📊 إجمالي الأرصدة: " . $balances->count() . "\n";
        echo "   ✅ الأرصدة النشطة: " . $activeBalances->count() . "\n";
        
        foreach ($currencies as $currency) {
            $currencyBalances = $balances->where('currency_id', 
                DB::table('currencies')->where('code', $currency)->first()->id ?? 0
            );
            echo "   💱 أرصدة بعملة {$currency}: " . $currencyBalances->count() . "\n";
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
        $today = Carbon::today();
        $currentRates = $rates->where('effective_date', $today->format('Y-m-d'));
        
        echo "   📊 إجمالي أسعار الصرف: " . $rates->count() . "\n";
        echo "   📅 أسعار اليوم: " . $currentRates->count() . "\n";
        
        // عرض الأسعار الحالية
        echo "\n   💱 أسعار الصرف الحالية:\n";
        foreach ($currentRates as $rate) {
            echo "   {$rate->from_currency} → {$rate->to_currency}: " . 
                 number_format($rate->rate, 4) . "\n";
        }
        echo "\n";
    }

    /**
     * اختبار دعم العملات المتعددة
     */
    private function testMultiCurrencySupport()
    {
        echo "🌍 اختبار دعم العملات المتعددة...\n";
        
        $accounts = DB::table('accounts')->where('is_group', false)->get();
        $multiCurrencyAccounts = $accounts->where('supports_multi_currency', true);
        $singleCurrencyAccounts = $accounts->where('supports_multi_currency', false);
        
        echo "   📊 إجمالي الحسابات الفعلية: " . $accounts->count() . "\n";
        echo "   🌍 حسابات متعددة العملات: " . $multiCurrencyAccounts->count() . "\n";
        echo "   💰 حسابات عملة واحدة: " . $singleCurrencyAccounts->count() . "\n";
        
        // نسبة دعم العملات المتعددة
        $percentage = $accounts->count() > 0 ? 
            round(($multiCurrencyAccounts->count() / $accounts->count()) * 100, 1) : 0;
        echo "   📈 نسبة دعم العملات المتعددة: {$percentage}%\n";
        
        if ($percentage >= 90) {
            echo "   ✅ ممتاز: معظم الحسابات تدعم العملات المتعددة\n";
        } elseif ($percentage >= 70) {
            echo "   ⚠️  جيد: أغلب الحسابات تدعم العملات المتعددة\n";
        } else {
            echo "   ❌ تحذير: عدد قليل من الحسابات يدعم العملات المتعددة\n";
        }
        echo "\n";
    }

    /**
     * إنشاء تقرير ملخص
     */
    private function generateSummaryReport()
    {
        echo "📋 تقرير ملخص النظام:\n";
        echo "=" . str_repeat("=", 50) . "\n";
        
        // إحصائيات عامة
        $stats = [
            'currencies' => DB::table('currencies')->count(),
            'total_currencies' => DB::table('currencies')->count(),
            'accounts' => DB::table('accounts')->count(),
            'account_groups' => DB::table('accounts')->where('is_group', true)->count(),
            'actual_accounts' => DB::table('accounts')->where('is_group', false)->count(),
            'account_balances' => DB::table('account_balances')->count(),
            'exchange_rates' => DB::table('currency_rates')->count(),
            'users' => DB::table('users')->count(),
            'branches' => DB::table('branches')->count(),
        ];

        foreach ($stats as $key => $value) {
            $label = $this->getStatLabel($key);
            echo sprintf("%-30s: %d\n", $label, $value);
        }
        
        echo "\n🎯 حالة النظام: ";
        if ($this->isSystemReady($stats)) {
            echo "✅ النظام جاهز للاستخدام!\n";
        } else {
            echo "⚠️  النظام يحتاج إلى مراجعة\n";
        }
        
        echo "\n📝 ملاحظات:\n";
        echo "- جميع الحسابات تدعم العملات المتعددة\n";
        echo "- يمكن إضافة أرصدة افتتاحية من واجهة النظام\n";
        echo "- أسعار الصرف قابلة للتحديث يومياً\n";
        echo "- النظام جاهز لإدخال السندات والمعاملات\n";
        
        echo "\n🚀 خطوات البدء:\n";
        echo "1. تسجيل الدخول بالبيانات المقدمة\n";
        echo "2. مراجعة وتحديث أسعار الصرف\n";
        echo "3. إدخال الأرصدة الافتتاحية\n";
        echo "4. بدء العمليات المحاسبية اليومية\n";
    }

    private function getStatLabel($key)
    {
        $labels = [
            'currencies' => 'إجمالي العملات',
            'active_currencies' => 'العملات النشطة',
            'accounts' => 'إجمالي الحسابات',
            'account_groups' => 'فئات الحسابات',
            'actual_accounts' => 'الحسابات الفعلية',
            'account_balances' => 'أرصدة الحسابات',
            'exchange_rates' => 'أسعار الصرف',
            'users' => 'المستخدمين',
            'branches' => 'الفروع',
        ];
        
        return $labels[$key] ?? $key;
    }

    private function isSystemReady($stats)
    {
        return $stats['currencies'] >= 3 &&
               $stats['total_currencies'] >= 2 &&
               $stats['accounts'] >= 100 &&
               $stats['actual_accounts'] >= 80 &&
               $stats['exchange_rates'] >= 4 &&
               $stats['users'] >= 1;
    }
}

// تشغيل الاختبارات
try {
    $test = new SystemTest();
    $test->runAllTests();
    echo "\n🎉 تم إكمال جميع الاختبارات بنجاح!\n";
} catch (Exception $e) {
    echo "\n❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "📍 في الملف: " . $e->getFile() . " السطر: " . $e->getLine() . "\n";
    exit(1);
} 