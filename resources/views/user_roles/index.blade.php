@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">إدارة أدوار المستخدمين</h1>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">قائمة المستخدمين والأدوار</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم المستخدم</th>
              <th>البريد الإلكتروني</th>
              <th>الأدوار</th>
              <th>إجراء</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach($user->roles as $role)
                    <span class="badge badge-info">{{ $role->name }}</span>
                  @endforeach
                </td>
                <td>
                  @if(!$user->isSuperAdmin())
                  <a href="{{ route('admin.user-roles.edit', $user->id) }}" class="btn btn-sm btn-primary">تعديل الأدوار</a>
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