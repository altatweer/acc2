@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">إدارة الصلاحيات</h1>
      </div>
      <div class="col-sm-6 text-left">
        <!-- لا يوجد زر إضافة -->
      </div>
    </div>
    <div class="alert alert-info mt-3" role="alert">
      هذه الصلاحيات نظامية ويتم تحديثها تلقائياً مع أي تطوير في النظام ولا يمكن تعديلها أو حذفها يدوياً.
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">قائمة الصلاحيات</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الصلاحية</th>
              <th>الوصف</th>
            </tr>
          </thead>
          <tbody>
            @foreach($permissions as $perm)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $perm->name }}</td>
                <td>{{ $perm->description }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection 