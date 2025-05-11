@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">تعديل أدوار المستخدم: {{ $user->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.user-roles.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">تعديل أدوار المستخدم</h3>
      </div>
      <form action="{{ route('admin.user-roles.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group">
            <label>الأدوار</label>
            <select name="roles[]" class="form-control" multiple required>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-warning">تحديث الأدوار</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection 