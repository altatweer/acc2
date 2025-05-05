@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">تعديل الدور: {{ $role->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">تعديل بيانات الدور</h3>
      </div>
      <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label>اسم الدور</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $role->name) }}">
          </div>
          <div class="form-group">
            <label>الصلاحيات</label>
            @php
              $permissionsBySection = [
                'المستخدمين' => ['عرض المستخدمين', 'إضافة مستخدم', 'تعديل مستخدم', 'حذف مستخدم'],
                'الأدوار' => ['عرض الأدوار', 'إضافة دور', 'تعديل دور', 'حذف دور'],
                'الصلاحيات' => ['عرض الصلاحيات', 'إضافة صلاحية', 'تعديل صلاحية', 'حذف صلاحية'],
                'الحسابات' => ['عرض الحسابات', 'إضافة حساب', 'تعديل حساب', 'حذف حساب'],
                'الفواتير' => ['عرض الفواتير', 'إضافة فاتورة', 'تعديل فاتورة', 'حذف فاتورة', 'تسديد فاتورة', 'طباعة فاتورة'],
                'السندات' => ['عرض السندات', 'إضافة سند', 'تعديل سند', 'حذف سند', 'طباعة سند'],
                'الحركات المالية' => ['عرض الحركات المالية', 'إضافة حركة مالية', 'تعديل حركة مالية', 'حذف حركة مالية'],
                'العملاء' => ['عرض العملاء', 'إضافة عميل', 'تعديل عميل', 'حذف عميل'],
                'العناصر' => ['عرض العناصر', 'إضافة عنصر', 'تعديل عنصر', 'حذف عنصر'],
                'الموظفين' => ['عرض الموظفين', 'إضافة موظف', 'تعديل موظف', 'حذف موظف'],
                'الرواتب' => ['عرض الرواتب', 'إضافة راتب', 'تعديل راتب', 'حذف راتب'],
                'دفعات الرواتب' => ['عرض دفعات الرواتب', 'إضافة دفعة راتب', 'تعديل دفعة راتب', 'حذف دفعة راتب'],
                'كشوف الرواتب' => ['عرض كشوف الرواتب', 'إضافة كشف رواتب', 'تعديل كشف رواتب', 'حذف كشف رواتب'],
                'العملات' => ['عرض العملات', 'إضافة عملة', 'تعديل عملة', 'حذف عملة'],
                'الفروع' => ['عرض الفروع', 'إضافة فرع', 'تعديل فرع', 'حذف فرع'],
                'الإعدادات' => ['عرض الإعدادات', 'تعديل الإعدادات', 'إدارة إعدادات النظام'],
                'القيود المحاسبية' => ['عرض القيود المحاسبية', 'إضافة قيد محاسبي', 'تعديل قيد محاسبي', 'حذف قيد محاسبي'],
              ];
            @endphp
            <div class="row">
              @foreach($permissionsBySection as $section => $perms)
                <div class="col-md-12 mb-2">
                  <strong style="font-size:1.1em; color:#007bff">{{ $section }}</strong>
                </div>
                @foreach($perms as $perm)
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="perm_{{ md5($perm) }}" {{ $role->permissions->contains('name', $perm) ? 'checked' : '' }}>
                      <label class="form-check-label" for="perm_{{ md5($perm) }}">{{ $perm }}</label>
                    </div>
                  </div>
                @endforeach
              @endforeach
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          @can('edit roles')
          <button type="submit" class="btn btn-warning">حفظ التعديلات</button>
          @endcan
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 