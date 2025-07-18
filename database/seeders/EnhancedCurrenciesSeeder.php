<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnhancedCurrenciesSeeder extends Seeder
{
    /**
     * إنشاء العملات المحسنة مع البيانات الكاملة
     */
    public function run()
    {
        $tenantId = 1;
        $now = Carbon::now();

        echo "💱 إنشاء العملات المحسنة مع البيانات الكاملة...\n";

        // حذف العملات الموجودة إذا كانت هناك أي
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('currencies')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // بيانات العملات الكاملة
        $currencies = [
            [
                'code' => 'IQD',
                'name' => 'Iraqi Dinar',
                'name_ar' => 'دينار عراقي',
                'symbol' => 'د.ع',
                'decimal_places' => 3,
                'exchange_rate' => 1.000000,
                'is_default' => true,
                'is_active' => true,
                'country' => 'Iraq',
                'country_ar' => 'العراق',
                'sort_order' => 1,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'name_ar' => 'دولار أمريكي',
                'symbol' => '$',
                'decimal_places' => 2,
                'exchange_rate' => 0.000763359,
                'is_default' => false,
                'is_active' => true,
                'country' => 'United States',
                'country_ar' => 'الولايات المتحدة',
                'sort_order' => 2,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'name_ar' => 'يورو',
                'symbol' => '€',
                'decimal_places' => 2,
                'exchange_rate' => 0.000689655,
                'is_default' => false,
                'is_active' => true,
                'country' => 'European Union',
                'country_ar' => 'الاتحاد الأوروبي',
                'sort_order' => 3,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'name_ar' => 'جنيه إسترليني',
                'symbol' => '£',
                'decimal_places' => 2,
                'exchange_rate' => 0.000610687,
                'is_default' => false,
                'is_active' => false,
                'country' => 'United Kingdom',
                'country_ar' => 'المملكة المتحدة',
                'sort_order' => 4,
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'name_ar' => 'ريال سعودي',
                'symbol' => 'ر.س',
                'decimal_places' => 2,
                'exchange_rate' => 0.002862595,
                'is_default' => false,
                'is_active' => false,
                'country' => 'Saudi Arabia',
                'country_ar' => 'المملكة العربية السعودية',
                'sort_order' => 5,
            ],
            [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'name_ar' => 'درهم إماراتي',
                'symbol' => 'د.إ',
                'decimal_places' => 2,
                'exchange_rate' => 0.002801678,
                'is_default' => false,
                'is_active' => false,
                'country' => 'United Arab Emirates',
                'country_ar' => 'الإمارات العربية المتحدة',
                'sort_order' => 6,
            ],
            [
                'code' => 'TRY',
                'name' => 'Turkish Lira',
                'name_ar' => 'ليرة تركية',
                'symbol' => '₺',
                'decimal_places' => 2,
                'exchange_rate' => 0.026041667,
                'is_default' => false,
                'is_active' => false,
                'country' => 'Turkey',
                'country_ar' => 'تركيا',
                'sort_order' => 7,
            ],
        ];

        // إدراج العملات الجديدة
        foreach ($currencies as $currency) {
            DB::table('currencies')->insert([
                'code' => $currency['code'],
                'name' => $currency['name'],
                'name_ar' => $currency['name_ar'],
                'symbol' => $currency['symbol'],
                'decimal_places' => $currency['decimal_places'],
                'exchange_rate' => $currency['exchange_rate'],
                'is_default' => $currency['is_default'],
                'is_active' => $currency['is_active'],
                'country' => $currency['country'],
                'country_ar' => $currency['country_ar'],
                'sort_order' => $currency['sort_order'],
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "✅ تم إنشاء " . count($currencies) . " عملة بنجاح\n";

        // إنشاء أسعار الصرف المحدثة
        $this->createExchangeRates($tenantId, $now);
        
        // إنشاء السجل التاريخي لأسعار الصرف
        $this->createExchangeRateHistory($tenantId, $now);

        echo "📊 العملات النشطة: " . collect($currencies)->where('is_active', true)->pluck('code')->implode(', ') . "\n";
        echo "💡 العملات القابلة للتفعيل: " . collect($currencies)->where('is_active', false)->pluck('code')->implode(', ') . "\n";
    }

    /**
     * إنشاء أسعار الصرف المحدثة
     */
    private function createExchangeRates($tenantId, $now)
    {
        echo "💱 إنشاء أسعار صرف محدثة...\n";

        // حذف الأسعار الموجودة
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('currency_rates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $rates = [
            // أسعار بيع العملات مقابل الدينار العراقي
            ['from' => 'USD', 'to' => 'IQD', 'rate' => 1310.0000000000, 'active' => true],
            ['from' => 'IQD', 'to' => 'USD', 'rate' => 0.0007633587, 'active' => true],
            ['from' => 'EUR', 'to' => 'IQD', 'rate' => 1450.0000000000, 'active' => true],
            ['from' => 'IQD', 'to' => 'EUR', 'rate' => 0.0006896552, 'active' => true],
            ['from' => 'USD', 'to' => 'EUR', 'rate' => 0.9034482759, 'active' => true],
            ['from' => 'EUR', 'to' => 'USD', 'rate' => 1.1068702291, 'active' => true],
            
            // أسعار العملات الخليجية (غير نشطة)
            ['from' => 'SAR', 'to' => 'IQD', 'rate' => 349.3333333333, 'active' => false],
            ['from' => 'IQD', 'to' => 'SAR', 'rate' => 0.0028625954, 'active' => false],
            ['from' => 'AED', 'to' => 'IQD', 'rate' => 356.8181818182, 'active' => false],
            ['from' => 'IQD', 'to' => 'AED', 'rate' => 0.0028016779, 'active' => false],
            
            // الليرة التركية
            ['from' => 'TRY', 'to' => 'IQD', 'rate' => 38.4000000000, 'active' => false],
            ['from' => 'IQD', 'to' => 'TRY', 'rate' => 0.0260416667, 'active' => false],
            
            // الجنيه الإسترليني
            ['from' => 'GBP', 'to' => 'IQD', 'rate' => 1637.5000000000, 'active' => false],
            ['from' => 'IQD', 'to' => 'GBP', 'rate' => 0.0006106870, 'active' => false],
        ];

        foreach ($rates as $rate) {
            DB::table('currency_rates')->insert([
                'from_currency' => $rate['from'],
                'to_currency' => $rate['to'],
                'rate' => $rate['rate'],
                'effective_date' => $now->format('Y-m-d'),
                'is_active' => $rate['active'],
                'notes' => 'سعر افتتاحي للنظام الجديد',
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "✅ تم إنشاء " . count($rates) . " سعر صرف\n";
    }

    /**
     * إنشاء السجل التاريخي لأسعار الصرف
     */
    private function createExchangeRateHistory($tenantId, $now)
    {
        echo "📈 إنشاء السجل التاريخي لأسعار الصرف...\n";

        $historyRates = [
            ['from' => 'USD', 'to' => 'IQD', 'rate' => 1310.0000000000, 'prev' => 1305.0000000000],
            ['from' => 'EUR', 'to' => 'IQD', 'rate' => 1450.0000000000, 'prev' => 1445.0000000000],
        ];

        foreach ($historyRates as $rate) {
            DB::table('exchange_rate_history')->insert([
                'from_currency' => $rate['from'],
                'to_currency' => $rate['to'],
                'rate' => $rate['rate'],
                'previous_rate' => $rate['prev'],
                'effective_date' => $now->format('Y-m-d'),
                'source' => 'system',
                'metadata' => json_encode([
                    'change_percentage' => round((($rate['rate'] - $rate['prev']) / $rate['prev']) * 100, 4),
                    'system_note' => 'إعداد النظام الجديد',
                ]),
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "✅ تم إنشاء السجل التاريخي\n";
    }
} 