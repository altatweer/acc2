<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // المستخدمين
            'view_users', 'add_user', 'edit_user', 'delete_user',
            // الأدوار
            'view_roles', 'add_role', 'edit_role', 'delete_role',
            // الصلاحيات
            'view_permissions', 'add_permission', 'edit_permission', 'delete_permission',
            // الحسابات
            'view_accounts', 'add_account', 'edit_account', 'delete_account',
            // الفواتير
            'view_invoices', 'add_invoice', 'edit_invoice', 'delete_invoice', 'pay_invoice', 'print_invoice',
            // السندات
            'view_vouchers', 'add_voucher', 'edit_voucher', 'delete_voucher', 'print_voucher', 'view_all_vouchers', 'cancel_vouchers',
            // الحركات المالية
            'view_transactions', 'add_transaction', 'edit_transaction', 'delete_transaction',
            // العملاء
            'view_customers', 'add_customer', 'edit_customer', 'delete_customer',
            // العناصر
            'view_items', 'add_item', 'edit_item', 'delete_item',
            // الموظفين
            'view_employees', 'add_employee', 'edit_employee', 'delete_employee',
            // الرواتب
            'view_salaries', 'add_salary', 'edit_salary', 'delete_salary',
            // دفعات الرواتب
            'view_salary_payments', 'add_salary_payment', 'edit_salary_payment', 'delete_salary_payment',
            // كشوف الرواتب
            'view_salary_batches', 'add_salary_batch', 'edit_salary_batch', 'delete_salary_batch',
            // العملات
            'view_currencies', 'add_currency', 'edit_currency', 'delete_currency',
            // الفروع
            'view_branches', 'add_branch', 'edit_branch', 'delete_branch',
            // الإعدادات
            'view_settings', 'edit_settings', 'manage_settings',
            // القيود المحاسبية
            'view_journal_entries', 'add_journal_entry', 'edit_journal_entry', 'delete_journal_entry', 'view_all_journal_entries', 'cancel_journal_entries',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        // إنشاء دور super-admin إذا لم يكن موجودًا
        Role::firstOrCreate(['name' => 'super-admin']);
    }
} 