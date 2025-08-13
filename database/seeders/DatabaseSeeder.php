<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // استدعاء Seeder الصلاحيات والأدوار أولاً
        $this->call(PermissionSeeder::class);
        
        // إضافة رخص التطوير الافتراضية
        $this->call(LicenseSeeder::class);
        
        // $this->call(AdminUserSeeder::class); // تم التعليق لأن الملف غير موجود
        // $this->call(CurrencySeeder::class); // تم التعليق لأن الملف غير موجود
        // $this->call(AccountsTableSeeder::class); // تم التعليق لأن الملف غير موجود

        // إنشاء مستخدم super admin افتراضي إذا لم يكن موجودًا
        $super = \App\Models\User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('admin12345'),
        ]);

        // صلاحيات شاملة لكل الأقسام
        $entities = [
            'users', 'roles', 'permissions', 'accounts', 'invoices', 'vouchers', 'transactions',
            'customers', 'items', 'employees', 'salaries', 'salary-payments', 'salary-batches',
            'currencies', 'branches', 'settings', 'journal-entries', 'tenants', 'subscription-plans'
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
        // إضافة الصلاحيات الأساسية إذا لم تكن موجودة
        $permissions[] = 'view_all_vouchers';
        $permissions[] = 'view_all_journal_entries';
        $permissions[] = 'cancel_vouchers';
        $permissions[] = 'cancel_journal_entries';
        // إضافة صلاحيات متعلقة بالمستأجرين
        $permissions[] = 'manage_tenants';
        $permissions[] = 'manage_subscriptions';
        $permissions[] = 'manage_tenant_features';
        
        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm);
        }

        // دور المدير العام
        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $role->syncPermissions($permissions);
        $super->assignRole($role);
        
        // تم إزالة multi-tenancy - لم تعد هناك حاجة لإنشاء مستأجرين
    }
}
