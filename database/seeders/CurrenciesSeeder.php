<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $tenantId = 1; // Default tenant

        // العملات الأساسية للنظام
        $currencies = [
            [
                'code' => 'IQD',
                'name' => 'Iraqi Dinar',
                'name_ar' => 'دينار عراقي',
                'symbol' => 'د.ع',
                'decimal_places' => 3,
                'rate' => 1.000000, // العملة الأساسية
                'is_active' => true,
                'is_base' => true,
                'country' => 'Iraq',
                'country_ar' => 'العراق'
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'name_ar' => 'دولار أمريكي', 
                'symbol' => '$',
                'decimal_places' => 2,
                'rate' => 1310.000000, // تحديث حسب السعر الحالي
                'is_active' => true,
                'is_base' => false,
                'country' => 'United States',
                'country_ar' => 'الولايات المتحدة'
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'name_ar' => 'يورو',
                'symbol' => '€',
                'decimal_places' => 2,
                'rate' => 1450.000000, // تحديث حسب السعر الحالي
                'is_active' => true,
                'is_base' => false,
                'country' => 'European Union',
                'country_ar' => 'الاتحاد الأوروبي'
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'name_ar' => 'جنيه إسترليني',
                'symbol' => '£',
                'decimal_places' => 2,
                'rate' => 1650.000000, // تحديث حسب السعر الحالي
                'is_active' => false, // غير نشط افتراضياً
                'is_base' => false,
                'country' => 'United Kingdom',
                'country_ar' => 'المملكة المتحدة'
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'name_ar' => 'ريال سعودي',
                'symbol' => 'ر.س',
                'decimal_places' => 2,
                'rate' => 349.000000, // تحديث حسب السعر الحالي
                'is_active' => false, // غير نشط افتراضياً
                'is_base' => false,
                'country' => 'Saudi Arabia',
                'country_ar' => 'السعودية'
            ],
            [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'name_ar' => 'درهم إماراتي',
                'symbol' => 'د.إ',
                'decimal_places' => 2,
                'rate' => 356.000000, // تحديث حسب السعر الحالي
                'is_active' => false, // غير نشط افتراضياً
                'is_base' => false,
                'country' => 'United Arab Emirates',
                'country_ar' => 'الإمارات العربية المتحدة'
            ],
            [
                'code' => 'TRY',
                'name' => 'Turkish Lira',
                'name_ar' => 'ليرة تركية',
                'symbol' => '₺',
                'decimal_places' => 2,
                'rate' => 43.500000, // تحديث حسب السعر الحالي
                'is_active' => false, // غير نشط افتراضياً
                'is_base' => false,
                'country' => 'Turkey',
                'country_ar' => 'تركيا'
            ]
        ];

        // حذف العملات الموجودة إذا كانت هناك أي
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('currencies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // إدراج العملات الجديدة
        foreach ($currencies as $currency) {
            DB::table('currencies')->insert([
                'code' => $currency['code'],
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'exchange_rate' => $currency['rate'],
                'is_default' => $currency['is_base'],
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // إضافة أسعار الصرف التاريخية للعملات النشطة
        $this->insertCurrencyRates();

        echo "✅ تم إنشاء " . count($currencies) . " عملة بنجاح\n";
        echo "📊 العملات النشطة: IQD (الأساسية), USD, EUR\n";
        echo "💡 يمكن تفعيل العملات الأخرى من لوحة الإدارة حسب الحاجة\n";
    }

    /**
     * إدراج أسعار الصرف الأولية
     */
    private function insertCurrencyRates(): void
    {
        $today = Carbon::today();
        $tenantId = 1;

        $rates = [
            // USD to IQD
            ['from' => 'USD', 'to' => 'IQD', 'rate' => 1310.0000000000],
            ['from' => 'IQD', 'to' => 'USD', 'rate' => 0.0007633588],
            
            // EUR to IQD
            ['from' => 'EUR', 'to' => 'IQD', 'rate' => 1450.0000000000],
            ['from' => 'IQD', 'to' => 'EUR', 'rate' => 0.0006896552],
            
            // USD to EUR (مباشر)
            ['from' => 'USD', 'to' => 'EUR', 'rate' => 0.9034482759],
            ['from' => 'EUR', 'to' => 'USD', 'rate' => 1.1068965517],
        ];

        // حذف الأسعار الموجودة
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('currency_rates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($rates as $rate) {
            DB::table('currency_rates')->insert([
                'from_currency' => $rate['from'],
                'to_currency' => $rate['to'],
                'rate' => $rate['rate'],
                'effective_date' => $today,
                'is_active' => true,
                'notes' => 'السعر الافتتاحي للنظام الجديد',
                'tenant_id' => $tenantId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        echo "💱 تم إنشاء " . count($rates) . " سعر صرف أولي\n";
    }
} 