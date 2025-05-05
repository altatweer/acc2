<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

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

        // إنشاء سوبر أدمن
        $super = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('admin12345'),
        ]);

        // صلاحيات شاملة لكل الأقسام
        $entities = [
            'users', 'roles', 'permissions', 'accounts', 'invoices', 'vouchers', 'transactions',
            'customers', 'items', 'employees', 'salaries', 'salary-payments', 'salary-batches',
            'currencies', 'branches', 'settings', 'journal-entries'
        ];
        $actions = ['view', 'create', 'edit', 'delete'];
        $permissions = [];
        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $permissions[] = "$action $entity";
            }
        }
        // إضافة صلاحيات للإعدادات الخاصة
        $permissions[] = 'manage settings';
        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm]);
        }

        // دور المدير العام
        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $role->syncPermissions($permissions);
        $super->assignRole($role);
    }
}
