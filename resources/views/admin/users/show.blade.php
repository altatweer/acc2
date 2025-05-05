@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">تفاصيل المستخدم: {{ $user->name }}</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">رجوع</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">بيانات المستخدم</h3>
      </div>
      <div class="card-body">
        <ul class="list-group">
          <li class="list-group-item"><strong>الاسم:</strong> {{ $user->name }}</li>
          <li class="list-group-item"><strong>البريد الإلكتروني:</strong> {{ $user->email }}</li>
          <li class="list-group-item"><strong>تاريخ الإنشاء:</strong> {{ $user->created_at }}</li>
        </ul>
      </div>
    </div>
  </div>
</section>
@endsection 