<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\AccountingSetting;

class CreateOpeningBalanceAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:create-opening-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء حساب الأرصدة الافتتاحية إذا لم يكن موجوداً';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // البحث عن حساب الأرصدة الافتتاحية الموجود
            $existingAccount = Account::where('name', 'الأرصدة الافتتاحية')->first();
            
            if ($existingAccount) {
                $this->info('حساب الأرصدة الافتتاحية موجود بالفعل: ' . $existingAccount->name . ' (' . $existingAccount->code . ')');
                
                // ربط الحساب في الإعدادات إذا لم يكن مربوطاً
                AccountingSetting::updateOrCreate(
                    ['key' => 'opening_balance_account'],
                    ['value' => $existingAccount->id]
                );
                
                return Command::SUCCESS;
            }

            // البحث عن فئة حقوق الملكية لوضع الحساب فيها
            $equityGroup = Account::where('type', 'equity')
                ->where('is_group', true)
                ->first();

            if (!$equityGroup) {
                // إنشاء فئة حقوق الملكية إذا لم تكن موجودة
                $equityGroup = Account::create([
                    'name' => 'حقوق الملكية',
                    'code' => '5000',
                    'type' => 'equity',
                    'nature' => null,
                    'is_group' => true,
                    'is_cash_box' => false,
                    'parent_id' => null,
                    'supports_multi_currency' => false,
                    'default_currency' => null,
                    'require_currency_selection' => false,
                    'tenant_id' => 1
                ]);
                
                $this->info('تم إنشاء فئة حقوق الملكية');
            }

            // البحث عن كود متاح لحساب الأرصدة الافتتاحية
            $baseCode = 5500;
            $code = $baseCode;
            while (Account::where('code', (string)$code)->exists()) {
                $code++;
            }

            // إنشاء حساب الأرصدة الافتتاحية
            $openingBalanceAccount = Account::create([
                'name' => 'الأرصدة الافتتاحية',
                'code' => (string)$code,
                'type' => 'equity',
                'nature' => 'credit',
                'is_group' => false,
                'is_cash_box' => false,
                'parent_id' => $equityGroup->id,
                'supports_multi_currency' => true,
                'default_currency' => 'IQD',
                'require_currency_selection' => false,
                'tenant_id' => 1
            ]);

            // ربط الحساب في الإعدادات
            AccountingSetting::updateOrCreate(
                ['key' => 'opening_balance_account'],
                ['value' => $openingBalanceAccount->id]
            );

            $this->info('تم إنشاء حساب الأرصدة الافتتاحية بنجاح!');
            $this->info('الكود: ' . $openingBalanceAccount->code);
            $this->info('الاسم: ' . $openingBalanceAccount->name);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('فشل في إنشاء حساب الأرصدة الافتتاحية: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
