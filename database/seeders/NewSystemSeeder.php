<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NewSystemSeeder extends Seeder
{
    /**
     * Seed the application's database for the new multi-currency system.
     */
    public function run(): void
    {
        echo "🚀 بدء إنشاء النظام المحاسبي الجديد متعدد العملات...\n\n";

        // 1. إنشاء المستخدم الافتراضي إذا لم يكن موجوداً
        $this->createDefaultUser();

        // 2. إنشاء العملات الأساسية
        echo "💱 إنشاء العملات الأساسية...\n";
        $this->call(CurrenciesSeeder::class);
        
        // 3. إنشاء الشجرة المحاسبية الجديدة
        echo "\n🌳 إنشاء الشجرة المحاسبية الجديدة...\n";
        $this->call(OptimalMultiCurrencyChartSeeder::class);

        // 4. إنشاء الفروع الافتراضية
        $this->createDefaultBranches();

        // 5. إنشاء الإعدادات الأساسية
        $this->createSystemSettings();

        echo "\n🎉 تم إنشاء النظام المحاسبي الجديد بنجاح!\n";
        echo "📋 ملخص النظام:\n";
        echo "   - العملات: " . DB::table('currencies')->count() . " عملة\n";
        echo "   - الحسابات: " . DB::table('accounts')->count() . " حساب\n";
        echo "   - الأرصدة: " . DB::table('account_balances')->count() . " رصيد\n";
        echo "   - أسعار الصرف: " . DB::table('currency_rates')->count() . " سعر\n";
        echo "\n🔐 بيانات الدخول:\n";
        echo "   البريد الإلكتروني: admin@company.com\n";
        echo "   كلمة المرور: password123\n";
        echo "\n✨ النظام جاهز للاستخدام!\n";
    }

    /**
     * إنشاء المستخدم الافتراضي
     */
    private function createDefaultUser(): void
    {
        $adminExists = DB::table('users')->where('email', 'admin@company.com')->exists();
        
        if (!$adminExists) {
            DB::table('users')->insert([
                'name' => 'مدير النظام',
                'email' => 'admin@company.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "👤 تم إنشاء المستخدم الافتراضي\n";
        } else {
            echo "👤 المستخدم الافتراضي موجود بالفعل\n";
        }
    }

    /**
     * إنشاء الفروع الافتراضية
     */
    private function createDefaultBranches(): void
    {
        $branchExists = DB::table('branches')->where('tenant_id', 1)->exists();
        
        if (!$branchExists) {
            DB::table('branches')->insert([
                'name' => 'الفرع الرئيسي',
                'location' => 'العنوان الرئيسي للشركة',
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            echo "🏢 تم إنشاء الفرع الرئيسي\n";
        } else {
            echo "🏢 الفرع الرئيسي موجود بالفعل\n";
        }
    }

    /**
     * إنشاء الإعدادات الأساسية للنظام
     */
    private function createSystemSettings(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'شركة التطوير التجاري', 'currency' => null],
            ['key' => 'company_name_en', 'value' => 'Commercial Development Company', 'currency' => null],
            ['key' => 'base_currency', 'value' => 'IQD', 'currency' => null],
            ['key' => 'decimal_places', 'value' => '3', 'currency' => 'IQD'],
            ['key' => 'decimal_places', 'value' => '2', 'currency' => 'USD'],
            ['key' => 'decimal_places', 'value' => '2', 'currency' => 'EUR'],
            ['key' => 'fiscal_year_start', 'value' => '01-01', 'currency' => null],
            ['key' => 'auto_update_exchange_rates', 'value' => 'false', 'currency' => null],
            ['key' => 'default_sales_account', 'value' => '4001', 'currency' => null],
            ['key' => 'default_purchases_account', 'value' => '5101', 'currency' => null],
            ['key' => 'default_cash_account', 'value' => '1011', 'currency' => null],
            ['key' => 'require_currency_on_transactions', 'value' => 'true', 'currency' => null],
            ['key' => 'allow_negative_balances', 'value' => 'false', 'currency' => null],
            ['key' => 'multi_currency_enabled', 'value' => 'true', 'currency' => null],
        ];

        // حذف الإعدادات الموجودة
        DB::table('accounting_settings')->where('tenant_id', 1)->delete();

        // إدراج الإعدادات الجديدة
        foreach ($settings as $setting) {
            DB::table('accounting_settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'currency' => $setting['currency'],
                'tenant_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        echo "⚙️ تم إنشاء الإعدادات الأساسية للنظام\n";
    }
} 