@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">تفاصيل العميل</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr><th>#</th><td>{{ $customer->id }}</td></tr>
            <tr><th>الاسم</th><td>{{ $customer->name }}</td></tr>
            <tr><th>البريد الإلكتروني</th><td>{{ $customer->email }}</td></tr>
            <tr><th>الهاتف</th><td>{{ $customer->phone ?? '-' }}</td></tr>
            <tr><th>العنوان</th><td>{{ $customer->address ?? '-' }}</td></tr>
            <tr><th>حساب الذمم</th><td>{{ $customer->account->name }}</td></tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">تعديل</a>
          <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">رجوع</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 