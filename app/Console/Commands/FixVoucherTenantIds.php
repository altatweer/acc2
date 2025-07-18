<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixVoucherTenantIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:voucher-tenant-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix tenant_id for vouchers and related tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء إصلاح tenant_id للسندات والجداول المتعلقة...');

        // جداول يجب إصلاحها
        $tables = [
            'vouchers',
            'transactions',
            'journal_entries',
            'journal_entry_lines',
            'accounts',
            'account_balances',
            'invoices',
            'invoice_items',
            'invoice_payments',
            'currencies',
            'branches',
            'users',
            'employees',
            'salaries',
            'salary_batches',
            'salary_payments',
            'customers',
            'items',
            'settings',
            'user_roles',
            'roles',
            'permissions',
            'languages',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'subscription_plans',
            'tenant_usage_stats',
            'tenant_features'
        ];

        $fixed = 0;

        foreach ($tables as $table) {
            try {
                // تحقق من وجود الجدول
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    $this->warn("الجدول {$table} غير موجود - تم تجاهله");
                    continue;
                }

                // تحقق من وجود عمود tenant_id
                if (!DB::getSchemaBuilder()->hasColumn($table, 'tenant_id')) {
                    $this->warn("عمود tenant_id غير موجود في الجدول {$table} - تم تجاهله");
                    continue;
                }

                // إصلاح السجلات التي لديها tenant_id NULL أو غير صحيح
                $updated = DB::table($table)
                    ->whereNull('tenant_id')
                    ->orWhere('tenant_id', '!=', 1)
                    ->update(['tenant_id' => 1]);

                if ($updated > 0) {
                    $this->info("تم إصلاح {$updated} سجل في الجدول {$table}");
                    $fixed += $updated;
                } else {
                    $this->comment("لا توجد سجلات تحتاج إصلاح في الجدول {$table}");
                }

            } catch (\Exception $e) {
                $this->error("خطأ في إصلاح الجدول {$table}: " . $e->getMessage());
            }
        }

        $this->info("تم إصلاح {$fixed} سجل إجمالي");
        $this->info('تم الانتهاء من إصلاح tenant_id بنجاح!');

        return 0;
    }
}
