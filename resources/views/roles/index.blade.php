@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">إدارة الأدوار والصلاحيات</h1>
      </div>
      <div class="col-sm-6 text-left">
        @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
        @if($isSuperAdmin || auth()->user()->can('create roles'))
        <a href="{{ route('admin.roles.create') }}" class="btn btn-success">إضافة دور جديد</a>
        @endif
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">قائمة الأدوار</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الدور</th>
              <th>الصلاحيات</th>
              <th>إجراء</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>
                  @foreach($role->permissions as $perm)
                    <span class="badge badge-info">{{ $perm->name }}</span>
                  @endforeach
                </td>
                <td>
                  @if($isSuperAdmin || auth()->user()->can('edit roles'))
                  <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                  @endif
                  @if($isSuperAdmin || auth()->user()->can('delete roles'))
                  <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف الدور؟')">حذف</button>
                  </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection 