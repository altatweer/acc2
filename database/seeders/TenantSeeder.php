<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود خطة الاشتراك أولاً
        $plan = DB::table('subscription_plans')->where('code', 'full_system')->first();
        
        if (!$plan) {
            // إنشاء خطة اشتراك افتراضية
            $planId = DB::table('subscription_plans')->insertGetId([
                'name' => 'خطة النظام الكاملة',
                'code' => 'full_system',
                'description' => 'خطة توفر كافة ميزات النظام دون قيود',
                'price' => 0.00, // مجاني للاستخدام المحلي
                'billing_cycle' => 'yearly',
                'trial_days' => 0,
                'features' => json_encode([
                    'accounts' => true,
                    'vouchers' => true,
                    'invoices' => true,
                    'reports' => true,
                    'multi_currency' => true,
                    'multi_branch' => true,
                    'employees' => true,
                    'customers' => true
                ]),
                'limits' => json_encode([
                    'users' => -1, // غير محدود
                    'accounts' => -1,
                    'vouchers' => -1,
                    'invoices' => -1,
                    'branches' => -1
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $planId = $plan->id;
        }

        // التحقق من وجود المستأجر أولاً
        $tenant = DB::table('tenants')->where('subdomain', 'main')->first();
        
        if (!$tenant) {
            // إنشاء المستأجر الافتراضي (رقم 1)
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'النظام الافتراضي',
                'subdomain' => 'main',
                'domain' => null,
                'database' => null,
                'contact_email' => 'admin@aursuite.com',
                'is_active' => true,
                'subscription_plan_id' => $planId,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addYears(10), // تاريخ بعيد للاستخدام المحلي
                'settings' => json_encode([
                    'locale' => 'ar',
                    'timezone' => 'Asia/Baghdad',
                    'currency' => 'IQD'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // إنشاء ميزات المستأجر
            $features = ['accounts', 'vouchers', 'invoices', 'reports', 'multi_currency', 'multi_branch', 'employees', 'customers'];
            foreach ($features as $feature) {
                DB::table('tenant_features')->insert([
                    'tenant_id' => $tenantId,
                    'feature_code' => $feature,
                    'is_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // إنشاء إحصائيات الاستخدام الافتراضية
            $resources = ['users', 'accounts', 'vouchers', 'invoices', 'branches'];
            foreach ($resources as $resource) {
                DB::table('tenant_usage_stats')->insert([
                    'tenant_id' => $tenantId,
                    'resource_type' => $resource,
                    'count' => 0,
                    'monthly_count' => 0,
                    'limit' => -1, // غير محدود
                    'last_updated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } else {
            $tenantId = $tenant->id;
        }

        // تحديث tenant_id لجميع المستخدمين الحاليين
        if (Schema::hasColumn('users', 'tenant_id')) {
            DB::table('users')->update(['tenant_id' => $tenantId]);
        }

        // تحديث tenant_id للأدوار الحالية
        if (Schema::hasColumn('roles', 'tenant_id')) {
            DB::table('roles')->update(['tenant_id' => $tenantId]);
        }
        
        // تحديث tenant_id للصلاحيات الحالية - تحقق من وجود العمود أولاً
        if (Schema::hasColumn('permissions', 'tenant_id')) {
            DB::table('permissions')->update(['tenant_id' => $tenantId]);
        }
        
        // تحديث tenant_id لجميع الجداول الأخرى المهمة
        $tablesToUpdate = [
            'accounts',
            'vouchers',
            'transactions',
            'invoices',
            'invoice_items',
            'branches',
            'currencies',
            'accounting_settings',
            'settings',
            'customers',
            'items',
            'employees',
            'salaries',
            'salary_batches',
            'salary_payments',
            'journal_entries',
            'journal_entry_lines'
        ];
        
        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                DB::table($table)->update(['tenant_id' => $tenantId]);
            }
        }
    }
}
