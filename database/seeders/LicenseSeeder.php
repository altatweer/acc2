<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\License;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء رخصة التطوير الافتراضية
        $developmentLicenses = [
            [
                'license_key' => 'DEV-2025-INTERNAL',
                'type' => 'development',
                'status' => 'active',
                'domain' => null, // يمكن استخدامها في أي دومين
                'expires_at' => now()->addYear(),
                'features' => [
                    'multi_currency' => true,
                    'reports_export' => true,
                    'api_access' => true,
                    'unlimited_users' => true,
                    'all_features' => true,
                    'chart_of_accounts' => true,
                    'invoicing' => true,
                    'payroll' => true,
                    'inventory' => true,
                    'banking' => true,
                    'reporting' => true
                ],
                'limits' => [
                    'max_users' => 999,
                    'max_companies' => 50,
                    'max_transactions' => null,
                    'max_accounts' => null,
                    'max_currencies' => null,
                    'storage_limit' => '10GB'
                ],
                'notes' => 'رخصة تطوير داخلية - صالحة لسنة واحدة من تاريخ الإنشاء'
            ],
            [
                'license_key' => 'DEV-2025-TESTING',
                'type' => 'development',
                'status' => 'active',
                'domain' => null,
                'expires_at' => now()->addMonths(6),
                'features' => [
                    'multi_currency' => true,
                    'reports_export' => true,
                    'api_access' => false,
                    'all_features' => true
                ],
                'limits' => [
                    'max_users' => 10,
                    'max_companies' => 5,
                    'max_transactions' => 1000,
                    'storage_limit' => '1GB'
                ],
                'notes' => 'رخصة تطوير للاختبار - محدودة بـ 6 أشهر'
            ]
        ];

        foreach ($developmentLicenses as $licenseData) {
            License::updateOrCreate(
                ['license_key' => $licenseData['license_key']],
                $licenseData
            );
        }

        $this->command->info('تم إنشاء رخص التطوير الافتراضية بنجاح');
        $this->command->info('رخصة التطوير الرئيسية: DEV-2025-INTERNAL');
        $this->command->info('رخصة الاختبار: DEV-2025-TESTING');
    }
}
