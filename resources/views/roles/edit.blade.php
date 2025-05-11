@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">@lang('messages.edit_role'): {{ $role->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">@lang('messages.back')</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('messages.role_data')</h3>
      </div>
      <form action="{{ Route::localizedRoute('admin.roles.update', ['role' => $role->id, ]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label>@lang('messages.role_name')</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $role->name) }}">
          </div>
          <div class="form-group">
            <label>@lang('messages.permissions')</label>
            @php
              $permissionsBySection = [
                'المستخدمين' => [
                  'عرض المستخدمين' => 'view_users',
                  'إضافة مستخدم' => 'add_user',
                  'تعديل مستخدم' => 'edit_user',
                  'حذف مستخدم' => 'delete_user',
                ],
                'الأدوار' => [
                  'عرض الأدوار' => 'view_roles',
                  'إضافة دور' => 'add_role',
                  'تعديل دور' => 'edit_role',
                  'حذف دور' => 'delete_role',
                ],
                'الصلاحيات' => [
                  'عرض الصلاحيات' => 'view_permissions',
                  'إضافة صلاحية' => 'add_permission',
                  'تعديل صلاحية' => 'edit_permission',
                  'حذف صلاحية' => 'delete_permission',
                ],
                'الحسابات' => [
                  'عرض الحسابات' => 'view_accounts',
                  'إضافة حساب' => 'add_account',
                  'تعديل حساب' => 'edit_account',
                  'حذف حساب' => 'delete_account',
                ],
                'الفواتير' => [
                  'عرض الفواتير' => 'view_invoices',
                  'إضافة فاتورة' => 'add_invoice',
                  'تعديل فاتورة' => 'edit_invoice',
                  'حذف فاتورة' => 'delete_invoice',
                  'تسديد فاتورة' => 'pay_invoice',
                  'طباعة فاتورة' => 'print_invoice',
                ],
                'السندات' => [
                  'عرض السندات' => 'view_vouchers',
                  'إضافة سند' => 'add_voucher',
                  'تعديل سند' => 'edit_voucher',
                  'حذف سند' => 'delete_voucher',
                  'طباعة سند' => 'print_voucher',
                  'جميع السندات' => 'view_all_vouchers',
                  'إلغاء سند' => 'cancel_vouchers',
                ],
                'الحركات المالية' => [
                  'عرض الحركات المالية' => 'view_transactions',
                  'إضافة حركة مالية' => 'add_transaction',
                  'تعديل حركة مالية' => 'edit_transaction',
                  'حذف حركة مالية' => 'delete_transaction',
                ],
                'العملاء' => [
                  'عرض العملاء' => 'view_customers',
                  'إضافة عميل' => 'add_customer',
                  'تعديل عميل' => 'edit_customer',
                  'حذف عميل' => 'delete_customer',
                ],
                'العناصر' => [
                  'عرض العناصر' => 'view_items',
                  'إضافة عنصر' => 'add_item',
                  'تعديل عنصر' => 'edit_item',
                  'حذف عنصر' => 'delete_item',
                ],
                'الموظفين' => [
                  'عرض الموظفين' => 'view_employees',
                  'إضافة موظف' => 'add_employee',
                  'تعديل موظف' => 'edit_employee',
                  'حذف موظف' => 'delete_employee',
                ],
                'الرواتب' => [
                  'عرض الرواتب' => 'view_salaries',
                  'إضافة راتب' => 'add_salary',
                  'تعديل راتب' => 'edit_salary',
                  'حذف راتب' => 'delete_salary',
                ],
                'دفعات الرواتب' => [
                  'عرض دفعات الرواتب' => 'view_salary_payments',
                  'إضافة دفعة راتب' => 'add_salary_payment',
                  'تعديل دفعة راتب' => 'edit_salary_payment',
                  'حذف دفعة راتب' => 'delete_salary_payment',
                ],
                'كشوف الرواتب' => [
                  'عرض كشوف الرواتب' => 'view_salary_batches',
                  'إضافة كشف رواتب' => 'add_salary_batch',
                  'تعديل كشف رواتب' => 'edit_salary_batch',
                  'حذف كشف رواتب' => 'delete_salary_batch',
                ],
                'العملات' => [
                  'عرض العملات' => 'view_currencies',
                  'إضافة عملة' => 'add_currency',
                  'تعديل عملة' => 'edit_currency',
                  'حذف عملة' => 'delete_currency',
                ],
                'الفروع' => [
                  'عرض الفروع' => 'view_branches',
                  'إضافة فرع' => 'add_branch',
                  'تعديل فرع' => 'edit_branch',
                  'حذف فرع' => 'delete_branch',
                ],
                'الإعدادات' => [
                  'عرض الإعدادات' => 'view_settings',
                  'تعديل الإعدادات' => 'edit_settings',
                  'إدارة إعدادات النظام' => 'manage_settings',
                ],
                'القيود المحاسبية' => [
                  'عرض القيود المحاسبية' => 'view_journal_entries',
                  'إضافة قيد محاسبي' => 'add_journal_entry',
                  'تعديل قيد محاسبي' => 'edit_journal_entry',
                  'حذف قيد محاسبي' => 'delete_journal_entry',
                  'جميع القيود المحاسبية' => 'view_all_journal_entries',
                  'إلغاء قيد محاسبي' => 'cancel_journal_entries',
                ],
              ];
            @endphp
            <div class="row">
              @foreach($permissionsBySection as $section => $perms)
                <div class="col-md-12 mb-2">
                  <strong style="font-size:1.1em; color:#007bff">{{ $section }}</strong>
                </div>
                @foreach($perms as $permLabel => $permName)
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permName }}" id="perm_{{ md5($permName) }}" {{ $role->permissions->contains('name', $permName) ? 'checked' : '' }}>
                      <label class="form-check-label" for="perm_{{ md5($permName) }}">{{ $permLabel }}</label>
                    </div>
                  </div>
                @endforeach
              @endforeach
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          @can('edit roles')
          <button type="submit" class="btn btn-warning">@lang('messages.save_changes')</button>
          @endcan
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 