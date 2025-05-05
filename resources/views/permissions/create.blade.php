@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">إضافة صلاحية جديدة</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">بيانات الصلاحية</h3>
      </div>
      <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group">
            <label>اسم الصلاحية</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>الوصف (اختياري)</label>
            <input type="text" name="description" class="form-control">
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 