<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء مستخدم افتراضي
     //   User::factory()->create([
       //     'name' => 'Test User',
         //   'email' => 'test@example.com',
  //      ]);

        // استدعاء Seeder الحسابات
        $this->call(CurrencySeeder::class);
        // استدعاء Seeder للشجرة الحسابية (الفئات والحسابات)
        $this->call(AccountsTableSeeder::class);
        // إنشاء مستخدم المدير الافتراضي
        $this->call(AdminUserSeeder::class);
        // استدعاء Seeder أرصدة الحسابات للعملة الافتراضية
        $this->call(AccountBalanceSeeder::class);

    }
}
