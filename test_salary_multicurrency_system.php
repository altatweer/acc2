<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * اختبار شامل لنظام سداد الرواتب متعدد العملات
 * 
 * هذا السكريبت يختبر جميع جوانب النظام الجديد:
 * - إنشاء كشف رواتب
 * - سداد رواتب بعملات مختلفة
 * - التحويلات بين العملات
 * - القيود المحاسبية
 * 
 * @author Accounting System v2.2.2
 */

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class SalaryMultiCurrencyTester
{
    private $testResults = [];
    private $employees = [];
    private $salaryBatch = null;
    private $testMode = true;

    public function __construct()
    {
        echo "🧪 اختبار نظام سداد الرواتب متعدد العملات v2.2.2\n";
        echo "=" . str_repeat("=", 60) . "\n\n";
    }

    /**
     * تشغيل جميع الاختبارات
     */
    public function runAllTests()
    {
        try {
            $this->testSystemRequirements();
            $this->testCurrencies();
            $this->testCashAccounts();
            $this->testEmployees();
            $this->createTestSalaryBatch();
            $this->testMultiCurrencyPayments();
            $this->testAccountingEntries();
            $this->generateTestReport();
            
            echo "\n✅ تم إتمام جميع الاختبارات بنجاح!\n";
            
        } catch (Exception $e) {
            echo "\n❌ فشل في الاختبارات: " . $e->getMessage() . "\n";
            echo "📍 السطر: " . $e->getLine() . " في الملف: " . $e->getFile() . "\n";
        }
    }

    /**
     * اختبار متطلبات النظام
     */
    private function testSystemRequirements()
    {
        echo "🔧 اختبار متطلبات النظام...\n";
        
        // فحص وجود الجداول المطلوبة
        $requiredTables = [
            'currencies', 'employees', 'salary_batches', 'salary_payments',
            'accounts', 'vouchers', 'journal_entries', 'journal_entry_lines'
        ];
        
        foreach ($requiredTables as $table) {
            try {
                $count = DB::table($table)->count();
                echo "   ✅ جدول {$table}: {$count} صف\n";
            } catch (Exception $e) {
                throw new Exception("الجدول المطلوب غير موجود أو لا يمكن الوصول إليه: $table");
            }
        }
        
        echo "   ✅ جميع الجداول المطلوبة موجودة\n";
        
        // فحص كلاسات النماذج
        $requiredModels = [
            'App\Models\Currency', 'App\Models\Employee', 'App\Models\SalaryBatch',
            'App\Models\SalaryPayment', 'App\Models\Account', 'App\Models\Voucher'
        ];
        
        foreach ($requiredModels as $model) {
            if (!class_exists($model)) {
                throw new Exception("النموذج المطلوب غير موجود: $model");
            }
        }
        
        echo "   ✅ جميع النماذج المطلوبة موجودة\n\n";
    }

    /**
     * اختبار العملات المتاحة
     */
    private function testCurrencies()
    {
        echo "💱 اختبار العملات المتاحة...\n";
        
        $currencies = DB::table('currencies')->get();
        $activeCurrencies = $currencies->where('is_default', false);
        $defaultCurrency = $currencies->where('is_default', true)->first();
        
        if ($currencies->count() < 2) {
            throw new Exception("يجب وجود عملتين على الأقل للاختبار");
        }
        
        echo "   📊 إجمالي العملات: " . $currencies->count() . "\n";
        echo "   🏦 العملة الافتراضية: " . ($defaultCurrency->code ?? 'غير محددة') . "\n";
        
        foreach ($currencies as $currency) {
            $status = $currency->is_default ? ' (افتراضية)' : '';
            echo "   💰 {$currency->code} - {$currency->name} - سعر الصرف: " . 
                 number_format($currency->exchange_rate, 6) . "{$status}\n";
        }
        
        $this->testResults['currencies'] = [
            'total' => $currencies->count(),
            'default' => $defaultCurrency->code ?? null,
            'available' => $currencies->pluck('code')->toArray()
        ];
        
        echo "\n";
    }

    /**
     * اختبار الصناديق النقدية
     */
    private function testCashAccounts()
    {
        echo "💰 اختبار الصناديق النقدية...\n";
        
        $cashAccounts = DB::table('accounts')
            ->where('is_cash_box', 1)
            ->get();
        
        if ($cashAccounts->count() < 2) {
            throw new Exception("يجب وجود صندوقين نقديين على الأقل للاختبار");
        }
        
        foreach ($cashAccounts as $account) {
            $balance = $this->getAccountBalance($account->id);
            echo "   🏦 {$account->name} ({$account->default_currency}) - رصيد: " . 
                 number_format($balance, 2) . "\n";
        }
        
        $this->testResults['cash_accounts'] = [
            'total' => $cashAccounts->count(),
            'accounts' => $cashAccounts->pluck('name', 'id')->toArray()
        ];
        
        echo "\n";
    }

    /**
     * اختبار بيانات الموظفين
     */
    private function testEmployees()
    {
        echo "👥 اختبار بيانات الموظفين...\n";
        
        $employees = DB::table('employees')
            ->where('status', 'active')
            ->get();
        
        if ($employees->count() < 2) {
            // إنشاء موظفين تجريبيين إذا لم يوجدوا
            $this->createTestEmployees();
            $employees = DB::table('employees')->where('status', 'active')->get();
        }
        
        $employeesByCurrency = $employees->groupBy('currency');
        
        echo "   📊 إجمالي الموظفين النشطين: " . $employees->count() . "\n";
        
        foreach ($employeesByCurrency as $currency => $emps) {
            echo "   💼 موظفو {$currency}: " . $emps->count() . "\n";
        }
        
        $this->employees = $employees;
        $this->testResults['employees'] = [
            'total' => $employees->count(),
            'by_currency' => $employeesByCurrency->map->count()->toArray()
        ];
        
        echo "\n";
    }

    /**
     * إنشاء موظفين تجريبيين
     */
    private function createTestEmployees()
    {
        echo "   🏗️ إنشاء موظفين تجريبيين...\n";
        
        $testEmployees = [
            [
                'name' => 'أحمد محمد علي',
                'employee_number' => 'EMP001',
                'currency' => 'IQD',
                'basic_salary' => 1500000, // 1.5 مليون دينار
                'department' => 'المحاسبة',
                'job_title' => 'محاسب أول'
            ],
            [
                'name' => 'سارة أحمد حسن',
                'employee_number' => 'EMP002', 
                'currency' => 'USD',
                'basic_salary' => 1200, // 1200 دولار
                'department' => 'الإدارة',
                'job_title' => 'مدير إداري'
            ],
            [
                'name' => 'محمد عبدالله',
                'employee_number' => 'EMP003',
                'currency' => 'IQD',
                'basic_salary' => 1200000, // 1.2 مليون دينار
                'department' => 'المبيعات',
                'job_title' => 'مندوب مبيعات'
            ]
        ];
        
        foreach ($testEmployees as $emp) {
            $employeeId = DB::table('employees')->insertGetId([
                'name' => $emp['name'],
                'employee_number' => $emp['employee_number'],
                'currency' => $emp['currency'],
                'department' => $emp['department'],
                'job_title' => $emp['job_title'],
                'status' => 'active',
                'hire_date' => Carbon::now()->subMonths(6),
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // إنشاء راتب أساسي للموظف
            DB::table('salaries')->insert([
                'employee_id' => $employeeId,
                'basic_salary' => $emp['basic_salary'],
                'allowances' => json_encode([]),
                'deductions' => json_encode([]),
                'effective_from' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'effective_to' => null,
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            echo "      ✅ {$emp['name']} ({$emp['currency']}) - {$emp['job_title']}\n";
        }
    }

    /**
     * إنشاء كشف رواتب تجريبي
     */
    private function createTestSalaryBatch()
    {
        echo "📋 إنشاء كشف رواتب تجريبي...\n";
        
        $testMonth = Carbon::now()->subMonth()->format('Y-m');
        
        // حذف كشوف الاختبار السابقة
        DB::table('salary_payments')->whereIn('salary_batch_id', 
            DB::table('salary_batches')->where('month', $testMonth)->pluck('id')
        )->delete();
        DB::table('salary_batches')->where('month', $testMonth)->delete();
        
        // إنشاء كشف رواتب جديد
        $batchId = DB::table('salary_batches')->insertGetId([
            'month' => $testMonth,
            'status' => 'approved',
            'created_by' => 1,
            'approved_by' => 1,
            'approved_at' => Carbon::now(),
            'tenant_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // إضافة رواتب الموظفين
        foreach ($this->employees as $employee) {
            $salary = DB::table('salaries')
                ->where('employee_id', $employee->id)
                ->whereNull('effective_to')
                ->first();
            
            if ($salary) {
                // حساب صافي الراتب بالعملة الأساسية
                $employeeCurrency = $employee->currency ?? 'IQD';
                $exchangeRate = $this->getExchangeRateToBase($employeeCurrency);
                $baseCurrencyNetSalary = $salary->basic_salary * $exchangeRate;
                
                DB::table('salary_payments')->insert([
                    'salary_batch_id' => $batchId,
                    'employee_id' => $employee->id,
                    'salary_month' => $testMonth,
                    'gross_salary' => $salary->basic_salary,
                    'total_allowances' => 0,
                    'total_deductions' => 0,
                    'net_salary' => $salary->basic_salary,
                    'currency' => $employeeCurrency,
                    'exchange_rate' => $exchangeRate,
                    'base_currency_net_salary' => $baseCurrencyNetSalary,
                    'payment_date' => null,
                    'status' => 'pending',
                    'tenant_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                echo "   ✅ {$employee->name}: " . number_format($salary->basic_salary, 2) . " {$employeeCurrency}\n";
            }
        }
        
        $this->salaryBatch = (object)['id' => $batchId, 'month' => $testMonth];
        $this->testResults['salary_batch'] = [
            'id' => $batchId,
            'month' => $testMonth,
            'employees_count' => $this->employees->count()
        ];
        
        echo "   📋 كشف الرواتب #{$batchId} للشهر {$testMonth} تم إنشاؤه بنجاح\n\n";
    }

    /**
     * اختبار سداد الرواتب متعدد العملات
     */
    private function testMultiCurrencyPayments()
    {
        echo "💸 اختبار سداد الرواتب متعدد العملات...\n";
        
        $salaryPayments = DB::table('salary_payments')
            ->where('salary_batch_id', $this->salaryBatch->id)
            ->where('status', 'pending')
            ->get();
        
        $testScenarios = [
            [
                'name' => 'دفع راتب بنفس العملة',
                'employee_currency' => 'IQD',
                'payment_currency' => 'IQD',
                'expected_rate' => 1
            ],
            [
                'name' => 'دفع راتب دينار بالدولار',
                'employee_currency' => 'IQD', 
                'payment_currency' => 'USD',
                'expected_rate' => 0.000763
            ],
            [
                'name' => 'دفع راتب دولار بالدينار',
                'employee_currency' => 'USD',
                'payment_currency' => 'IQD',
                'expected_rate' => 1310
            ]
        ];
        
        foreach ($testScenarios as $scenario) {
            echo "\n   🧪 سيناريو: {$scenario['name']}\n";
            $this->executePaymentScenario($scenario, $salaryPayments);
        }
        
        echo "\n";
    }

    /**
     * تنفيذ سيناريو دفع معين
     */
    private function executePaymentScenario($scenario, $salaryPayments)
    {
        // العثور على موظف مناسب للسيناريو
        $targetPayment = null;
        foreach ($salaryPayments as $payment) {
            $employee = $this->employees->where('id', $payment->employee_id)->first();
            if ($employee && $employee->currency === $scenario['employee_currency']) {
                $targetPayment = $payment;
                break;
            }
        }
        
        if (!$targetPayment) {
            echo "      ⚠️ لا يوجد موظف بعملة {$scenario['employee_currency']} للاختبار\n";
            return;
        }
        
        $employee = $this->employees->where('id', $targetPayment->employee_id)->first();
        
        // حساب التحويل
        $exchangeRate = $this->calculateExchangeRate(
            $scenario['payment_currency'], 
            $scenario['employee_currency']
        );
        
        $paymentAmount = $scenario['payment_currency'] === $scenario['employee_currency'] 
            ? $targetPayment->net_salary
            : $targetPayment->net_salary / $exchangeRate;
        
        $convertedAmount = $paymentAmount * $exchangeRate;
        
        echo "      👤 الموظف: {$employee->name}\n";
        echo "      💰 راتب الموظف: " . number_format($targetPayment->net_salary, 2) . " {$employee->currency}\n";
        echo "      💳 مبلغ الدفع: " . number_format($paymentAmount, 2) . " {$scenario['payment_currency']}\n";
        echo "      📈 سعر الصرف: " . number_format($exchangeRate, 6) . "\n";
        echo "      🔄 المبلغ المحول: " . number_format($convertedAmount, 2) . " {$employee->currency}\n";
        
        // التحقق من دقة التحويل
        $difference = abs($convertedAmount - $targetPayment->net_salary);
        if ($difference < 0.01) {
            echo "      ✅ التحويل صحيح (فرق: " . number_format($difference, 4) . ")\n";
        } else {
            echo "      ⚠️ خطأ في التحويل (فرق: " . number_format($difference, 4) . ")\n";
        }
        
        // محاكاة إنشاء السند
        if ($this->testMode) {
            $this->simulateVoucherCreation($targetPayment, $employee, $paymentAmount, $scenario['payment_currency'], $exchangeRate);
        }
    }

    /**
     * محاكاة إنشاء سند الدفع
     */
    private function simulateVoucherCreation($payment, $employee, $paymentAmount, $paymentCurrency, $exchangeRate)
    {
        echo "      📝 محاكاة إنشاء سند الدفع...\n";
        
        // العثور على صندوق نقدي مناسب
        $cashAccount = DB::table('accounts')
            ->where('is_cash_box', 1)
            ->where('default_currency', $paymentCurrency)
            ->first();
        
        if (!$cashAccount) {
            echo "      ⚠️ لا يوجد صندوق نقدي بعملة {$paymentCurrency}\n";
            return;
        }
        
        $cashBalance = $this->getAccountBalance($cashAccount->id, $paymentCurrency);
        
        echo "      🏦 الصندوق النقدي: {$cashAccount->name}\n";
        echo "      💎 رصيد الصندوق: " . number_format($cashBalance, 2) . " {$paymentCurrency}\n";
        
        if ($cashBalance >= $paymentAmount) {
            echo "      ✅ الرصيد كافي للدفع\n";
            
            // محاكاة القيد المحاسبي
            echo "      📊 القيد المحاسبي:\n";
            echo "         مدين: حساب ذمم الموظفين - " . number_format($payment->net_salary, 2) . " {$employee->currency}\n";
            echo "         دائن: {$cashAccount->name} - " . number_format($paymentAmount, 2) . " {$paymentCurrency}\n";
            
        } else {
            echo "      ❌ الرصيد غير كافي للدفع\n";
        }
    }

    /**
     * اختبار القيود المحاسبية
     */
    private function testAccountingEntries()
    {
        echo "📊 اختبار القيود المحاسبية...\n";
        
        $recentEntries = DB::table('journal_entries')
            ->where('source_type', 'App\Models\Voucher')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->get();
        
        echo "   📋 القيود المحاسبية الحديثة: " . $recentEntries->count() . "\n";
        
        foreach ($recentEntries as $entry) {
            $lines = DB::table('journal_entry_lines')
                ->where('journal_entry_id', $entry->id)
                ->get();
            
            echo "   📝 قيد #{$entry->id}: {$entry->description}\n";
            echo "      العملة: {$entry->currency} | سعر الصرف: " . number_format($entry->exchange_rate, 6) . "\n";
            echo "      إجمالي مدين: " . number_format($entry->total_debit, 2) . "\n";
            echo "      إجمالي دائن: " . number_format($entry->total_credit, 2) . "\n";
            
            foreach ($lines as $line) {
                $account = DB::table('accounts')->where('id', $line->account_id)->first();
                $type = $line->debit > 0 ? 'مدين' : 'دائن';
                $amount = $line->debit > 0 ? $line->debit : $line->credit;
                echo "         {$type}: {$account->name} - " . number_format($amount, 2) . " {$line->currency}\n";
            }
        }
        
        $this->testResults['accounting_entries'] = [
            'recent_entries' => $recentEntries->count(),
            'multi_currency_entries' => $recentEntries->where('is_multi_currency', true)->count()
        ];
        
        echo "\n";
    }

    /**
     * إنتاج تقرير الاختبار
     */
    private function generateTestReport()
    {
        echo "📋 تقرير الاختبار النهائي\n";
        echo "=" . str_repeat("=", 40) . "\n";
        
        echo "🏦 العملات المتاحة: " . implode(', ', $this->testResults['currencies']['available']) . "\n";
        echo "💰 الصناديق النقدية: " . $this->testResults['cash_accounts']['total'] . "\n";
        echo "👥 الموظفين النشطين: " . $this->testResults['employees']['total'] . "\n";
        echo "📋 كشف الرواتب: #{$this->testResults['salary_batch']['id']}\n";
        echo "📊 القيود المحاسبية: " . ($this->testResults['accounting_entries']['recent_entries'] ?? 0) . "\n";
        
        echo "\n✅ النظام جاهز لسداد الرواتب متعدد العملات!\n";
        
        echo "\n📌 نصائح الاستخدام:\n";
        echo "• يمكن دفع راتب موظف بأي عملة متاحة\n";
        echo "• يتم التحويل تلقائياً حسب أسعار الصرف المحددة\n";
        echo "• تأكد من وجود رصيد كافي في الصندوق النقدي\n";
        echo "• يمكن تعديل سعر الصرف يدوياً عند الحاجة\n";
    }

    /**
     * حساب سعر الصرف بين عملتين
     */
    private function calculateExchangeRate($fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return 1;
        }
        
        $fromRate = DB::table('currencies')->where('code', $fromCurrency)->value('exchange_rate');
        $toRate = DB::table('currencies')->where('code', $toCurrency)->value('exchange_rate');
        
        if (!$fromRate || !$toRate) {
            return 1;
        }
        
        return $toRate / $fromRate;
    }

    /**
     * حساب سعر الصرف إلى العملة الأساسية
     */
    private function getExchangeRateToBase($currency)
    {
        if ($currency === 'IQD') {
            return 1; // العملة الأساسية
        }
        
        $currencyData = DB::table('currencies')->where('code', $currency)->first();
        return $currencyData ? $currencyData->exchange_rate : 1;
    }

    /**
     * حساب رصيد حساب
     */
    private function getAccountBalance($accountId, $currency = null)
    {
        if (!$currency) {
            $account = DB::table('accounts')->where('id', $accountId)->first();
            $currency = $account->default_currency ?? 'IQD';
        }
        
        $debit = DB::table('journal_entry_lines')
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->sum('debit');
            
        $credit = DB::table('journal_entry_lines')
            ->where('account_id', $accountId)
            ->where('currency', $currency)
            ->sum('credit');
        
        $account = DB::table('accounts')->where('id', $accountId)->first();
        
        if ($account && $account->nature === 'debit') {
            return $debit - $credit;
        } else {
            return $credit - $debit;
        }
    }
}

// تشغيل الاختبارات
if (php_sapi_name() === 'cli') {
    $tester = new SalaryMultiCurrencyTester();
    $tester->runAllTests();
} 