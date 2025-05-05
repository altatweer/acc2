@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">تعديل المستخدم: {{ $user->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">تعديل بيانات المستخدم</h3>
      </div>
      <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
          </div>
          <div class="form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
          </div>
          <div class="form-group">
            <label>كلمة المرور (اتركها فارغة إذا لا تريد التغيير)</label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="form-group">
            <label>تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" class="form-control">
          </div>
          <div class="form-group">
            <label>الأدوار</label>
            <div class="row">
              @foreach($roles as $role)
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ $user->roles->contains('name', $role->name) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-warning">حفظ التعديلات</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 