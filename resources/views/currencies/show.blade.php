@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">عرض تفاصيل العملة</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr>
              <th>#</th>
              <td>{{ $currency->id }}</td>
            </tr>
            <tr>
              <th>الاسم</th>
              <td>{{ $currency->name }}</td>
            </tr>
            <tr>
              <th>رمز العملة</th>
              <td>{{ $currency->code }}</td>
            </tr>
            <tr>
              <th>الرمز النصي</th>
              <td>{{ $currency->symbol }}</td>
            </tr>
            <tr>
              <th>سعر الصرف</th>
              <td>{{ $currency->exchange_rate }}</td>
            </tr>
            <tr>
              <th>الافتراضية</th>
              <td>
                @if($currency->is_default)
                  نعم
                @else
                  لا
                @endif
              </td>
            </tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ route('currencies.edit', $currency->id) }}" class="btn btn-warning">تعديل</a>
          <a href="{{ route('currencies.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 