@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">تعديل الصلاحية: {{ $permission->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">تعديل بيانات الصلاحية</h3>
      </div>
      <form action="{{ route('admin.permissions.update'permission$permission) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label>اسم الصلاحية</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required>
          </div>
          <div class="form-group">
            <label>الوصف (اختياري)</label>
            <input type="text" name="description" class="form-control" value="{{ old('description', $permission->description) }}">
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-warning">تحديث</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 