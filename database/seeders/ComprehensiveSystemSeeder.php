<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveSystemSeeder extends Seeder
{
    /**
     * إنشاء النظام المحاسبي الشامل المحدث
     */
    public function run()
    {
        echo "🚀 بدء إنشاء النظام المحاسبي الشامل المحدث...\n\n";

        // إنشاء المستخدم الافتراضي
        $this->createDefaultUser();

        // إنشاء العملات المحسنة
        $this->call(EnhancedCurrenciesSeeder::class);

        // إنشاء الشجرة المحاسبية
        $this->call(OptimalMultiCurrencyChartSeeder::class);

        // إنشاء الفرع الرئيسي
        $this->createMainBranch();

        // إنشاء بيانات تجريبية محسنة
        $this->createEnhancedSampleData();

        echo "\n🎉 تم إنشاء النظام المحاسبي الشامل المحدث بنجاح!\n";
        echo "📊 النظام جاهز للاستخدام مع جميع الميزات المحسنة\n";
    }

    /**
     * إنشاء المستخدم الافتراضي
     */
    private function createDefaultUser()
    {
        echo "👤 إنشاء المستخدم الافتراضي...\n";

        $userExists = DB::table('users')->where('email', 'admin@company.com')->exists();

        if (!$userExists) {
            DB::table('users')->insert([
                'name' => 'مدير النظام',
                'email' => 'admin@company.com',
                'password' => Hash::make('password123'),
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "✅ تم إنشاء المستخدم الافتراضي\n";
        } else {
            echo "👤 المستخدم الافتراضي موجود بالفعل\n";
        }
    }

    /**
     * إنشاء الفرع الرئيسي
     */
    private function createMainBranch()
    {
        echo "🏢 إنشاء الفرع الرئيسي...\n";

        $branchExists = DB::table('branches')->where('name', 'الفرع الرئيسي')->exists();

        if (!$branchExists) {
            DB::table('branches')->insert([
                'name' => 'الفرع الرئيسي',
                'location' => 'العنوان الرئيسي للشركة',
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "✅ تم إنشاء الفرع الرئيسي\n";
        } else {
            echo "🏢 الفرع الرئيسي موجود بالفعل\n";
        }
    }

    /**
     * إنشاء بيانات تجريبية محسنة
     */
    private function createEnhancedSampleData()
    {
        echo "\n📦 إنشاء بيانات تجريبية محسنة...\n";

        // إنشاء عملاء تجريبيين
        $this->createSampleCustomers();

        // إنشاء موظفين تجريبيين
        $this->createSampleEmployees();

        // إنشاء منتجات تجريبية
        $this->createSampleItems();

        // إنشاء أرصدة افتتاحية متعددة العملات
        $this->createMultiCurrencyOpeningBalances();
    }

    /**
     * إنشاء عملاء تجريبيين
     */
    private function createSampleCustomers()
    {
        echo "👥 إنشاء عملاء تجريبيين...\n";

        $customers = [
            [
                'name' => 'شركة البناء المتحدة',
                'email' => 'united.construction@email.com',
                'phone' => '+964 770 123 4567',
                'address' => 'بغداد - الكرادة',
                'default_currency' => 'IQD',
                'credit_limit' => 5000000.00,
                'credit_limit_currency' => 'IQD',
            ],
            [
                'name' => 'شركة النفط الدولية',
                'email' => 'international.oil@email.com',
                'phone' => '+964 771 987 6543',
                'address' => 'البصرة - المنطقة الصناعية',
                'default_currency' => 'USD',
                'credit_limit' => 10000.00,
                'credit_limit_currency' => 'USD',
            ],
            [
                'name' => 'مؤسسة التجارة الأوروبية',
                'email' => 'euro.trading@email.com',
                'phone' => '+964 772 555 1234',
                'address' => 'أربيل - المنطقة التجارية',
                'default_currency' => 'EUR',
                'credit_limit' => 8000.00,
                'credit_limit_currency' => 'EUR',
            ],
        ];

        foreach ($customers as $customerData) {
            // إنشاء حساب العميل
            $accountId = DB::table('accounts')->insertGetId([
                'name' => 'حساب عميل - ' . $customerData['name'],
                'code' => '1201' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'parent_id' => DB::table('accounts')->where('code', '1201')->first()->id,
                'type' => 'asset',
                'nature' => 'debit',
                'is_cash_box' => false,
                'supports_multi_currency' => true,
                'default_currency' => $customerData['default_currency'],
                'require_currency_selection' => false,
                'is_group' => false,
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // إنشاء العميل
            $customerId = DB::table('customers')->insertGetId([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'address' => $customerData['address'],
                'account_id' => $accountId,
                'default_currency' => $customerData['default_currency'],
                'credit_limit' => $customerData['credit_limit'],
                'credit_limit_currency' => $customerData['credit_limit_currency'],
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // إنشاء رصيد العميل
            $currencyId = DB::table('currencies')->where('code', $customerData['default_currency'])->first()->id;
            DB::table('customer_balances')->insert([
                'customer_id' => $customerId,
                'currency' => $customerData['default_currency'],
                'balance' => 0.00,
                'credit_limit' => $customerData['credit_limit'],
                'is_active' => true,
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        echo "✅ تم إنشاء " . count($customers) . " عميل تجريبي\n";
    }

    /**
     * إنشاء موظفين تجريبيين
     */
    private function createSampleEmployees()
    {
        echo "👨‍💼 إنشاء موظفين تجريبيين...\n";

        $employees = [
            [
                'name' => 'أحمد محمود',
                'employee_number' => 'EMP001',
                'department' => 'المحاسبة',
                'job_title' => 'محاسب رئيسي',
                'currency' => 'IQD',
                'salary_currency' => 'IQD',
                'base_salary' => 1200000.00,
            ],
            [
                'name' => 'فاطمة علي',
                'employee_number' => 'EMP002',
                'department' => 'المبيعات',
                'job_title' => 'مديرة مبيعات',
                'currency' => 'USD',
                'salary_currency' => 'USD',
                'base_salary' => 800.00,
            ],
            [
                'name' => 'محمد حسن',
                'employee_number' => 'EMP003',
                'department' => 'التقنية',
                'job_title' => 'مطور أنظمة',
                'currency' => 'EUR',
                'salary_currency' => 'EUR',
                'base_salary' => 700.00,
            ],
        ];

        foreach ($employees as $employeeData) {
            DB::table('employees')->insert([
                'name' => $employeeData['name'],
                'employee_number' => $employeeData['employee_number'],
                'department' => $employeeData['department'],
                'job_title' => $employeeData['job_title'],
                'hire_date' => Carbon::now()->subMonths(rand(1, 24)),
                'status' => 'active',
                'currency' => $employeeData['currency'],
                'salary_currency' => $employeeData['salary_currency'],
                'base_salary' => $employeeData['base_salary'],
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        echo "✅ تم إنشاء " . count($employees) . " موظف تجريبي\n";
    }

    /**
     * إنشاء منتجات تجريبية
     */
    private function createSampleItems()
    {
        echo "📦 إنشاء منتجات تجريبية...\n";

        $items = [
            [
                'name' => 'خدمة استشارات إدارية',
                'type' => 'service',
                'unit_price' => 150000.00,
                'currency' => 'IQD',
                'cost_price' => 75000.00,
                'cost_currency' => 'IQD',
                'description' => 'خدمات استشارية في مجال الإدارة والتطوير',
            ],
            [
                'name' => 'برنامج محاسبي متقدم',
                'type' => 'product',
                'unit_price' => 500.00,
                'currency' => 'USD',
                'cost_price' => 300.00,
                'cost_currency' => 'USD',
                'description' => 'برنامج محاسبي شامل متعدد العملات',
            ],
            [
                'name' => 'دورة تدريبية مالية',
                'type' => 'service',
                'unit_price' => 400.00,
                'currency' => 'EUR',
                'cost_price' => 200.00,
                'cost_currency' => 'EUR',
                'description' => 'دورات تدريبية في المحاسبة والتمويل',
            ],
        ];

        foreach ($items as $itemData) {
            $itemId = DB::table('items')->insertGetId([
                'name' => $itemData['name'],
                'type' => $itemData['type'],
                'unit_price' => $itemData['unit_price'],
                'currency' => $itemData['currency'],
                'cost_price' => $itemData['cost_price'],
                'cost_currency' => $itemData['cost_currency'],
                'is_multi_currency' => true,
                'description' => $itemData['description'],
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // إنشاء أسعار متعددة العملات للمنتج
            $currencies = ['IQD', 'USD', 'EUR'];
            foreach ($currencies as $currency) {
                $exchangeRate = DB::table('currencies')->where('code', $currency)->first()->exchange_rate;
                $basePrice = $itemData['unit_price'];
                
                if ($itemData['currency'] !== $currency) {
                    if ($currency === 'IQD') {
                        $price = $basePrice / $exchangeRate;
                    } else {
                        $baseCurrencyRate = DB::table('currencies')->where('code', $itemData['currency'])->first()->exchange_rate;
                        $price = ($basePrice * $baseCurrencyRate) / $exchangeRate;
                    }
                } else {
                    $price = $basePrice;
                }

                DB::table('item_prices')->insert([
                    'item_id' => $itemId,
                    'currency' => $currency,
                    'price' => round($price, 4),
                    'price_type' => 'selling',
                    'is_active' => true,
                    'effective_from' => Carbon::now(),
                    'notes' => 'سعر افتتاحي للنظام الجديد',
                    'tenant_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        echo "✅ تم إنشاء " . count($items) . " منتج تجريبي مع أسعار متعددة العملات\n";
    }

    /**
     * إنشاء أرصدة افتتاحية متعددة العملات
     */
    private function createMultiCurrencyOpeningBalances()
    {
        echo "💰 إنشاء أرصدة افتتاحية متعددة العملات...\n";

        $openingBalances = [
            // الصناديق
            ['account_code' => '1101', 'currency' => 'IQD', 'balance' => 5000000.00],
            ['account_code' => '1101', 'currency' => 'USD', 'balance' => 2000.00],
            ['account_code' => '1102', 'currency' => 'IQD', 'balance' => 1000000.00],
            
            // البنوك
            ['account_code' => '1110', 'currency' => 'IQD', 'balance' => 50000000.00],
            ['account_code' => '1111', 'currency' => 'USD', 'balance' => 15000.00],
            ['account_code' => '1112', 'currency' => 'EUR', 'balance' => 8000.00],
            
            // رأس المال
            ['account_code' => '3101', 'currency' => 'IQD', 'balance' => 100000000.00],
        ];

        foreach ($openingBalances as $balance) {
            $account = DB::table('accounts')->where('code', $balance['account_code'])->first();
            $currency = DB::table('currencies')->where('code', $balance['currency'])->first();
            
            if ($account && $currency) {
                // تحديث أو إنشاء رصيد الحساب
                DB::table('account_balances')->updateOrInsert(
                    [
                        'account_id' => $account->id,
                        'currency_id' => $currency->id,
                        'tenant_id' => 1,
                    ],
                    [
                        'balance' => $balance['balance'],
                        'last_transaction_date' => Carbon::now(),
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        }

        echo "✅ تم إنشاء " . count($openingBalances) . " رصيد افتتاحي متعدد العملات\n";
    }
} 