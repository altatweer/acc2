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
                'users' => [
                  'view_users', 'add_user', 'edit_user', 'delete_user',
                ],
                'roles' => [
                  'view_roles', 'add_role', 'edit_role', 'delete_role',
                ],
                'permissions' => [
                  'view_permissions', 'add_permission', 'edit_permission', 'delete_permission',
                ],
                'accounts' => [
                  'view_accounts', 'add_account', 'edit_account', 'delete_account',
                ],
                'invoices' => [
                  'view_invoices', 'add_invoice', 'edit_invoice', 'delete_invoice', 'pay_invoice', 'print_invoice',
                ],
                'vouchers' => [
                  'view_vouchers', 'add_voucher', 'edit_voucher', 'delete_voucher', 'print_voucher', 'view_all_vouchers', 'cancel_vouchers',
                ],
                'transactions' => [
                  'view_transactions', 'add_transaction', 'edit_transaction', 'delete_transaction',
                ],
                'customers' => [
                  'view_customers', 'add_customer', 'edit_customer', 'delete_customer',
                ],
                'items' => [
                  'view_items', 'add_item', 'edit_item', 'delete_item',
                ],
                'employees' => [
                  'view_employees', 'add_employee', 'edit_employee', 'delete_employee',
                ],
                'salaries' => [
                  'view_salaries', 'add_salary', 'edit_salary', 'delete_salary',
                ],
                'salary_payments' => [
                  'view_salary_payments', 'add_salary_payment', 'edit_salary_payment', 'delete_salary_payment',
                ],
                'salary_batches' => [
                  'view_salary_batches', 'add_salary_batch', 'edit_salary_batch', 'delete_salary_batch',
                ],
                'currencies' => [
                  'view_currencies', 'add_currency', 'edit_currency', 'delete_currency',
                ],
                'branches' => [
                  'view_branches', 'add_branch', 'edit_branch', 'delete_branch',
                ],
                'settings' => [
                  'view_settings', 'edit_settings', 'manage_settings',
                ],
                'journal_entries' => [
                  'view_journal_entries', 'add_journal_entry', 'edit_journal_entry', 'delete_journal_entry', 'view_all_journal_entries', 'cancel_journal_entries',
                ],
              ];
            @endphp
            <div class="row">
              @foreach($permissionsBySection as $section => $perms)
                <div class="col-md-12 mb-2">
                  <strong style="font-size:1.1em; color:#007bff">@lang('messages.' . $section)</strong>
                </div>
                @foreach($perms as $perm)
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="perm_{{ md5($perm) }}" {{ $role->permissions->contains('name', $perm) ? 'checked' : '' }}>
                      <label class="form-check-label" for="perm_{{ md5($perm) }}">@lang('messages.' . $perm)</label>
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