@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">إدارة العملات</h1>
        </div>
        <div class="col-sm-6 text-left">
          <a href="{{ route('currencies.create') }}" class="btn btn-primary">إضافة عملة جديدة</a>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">قائمة العملات</h3>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الرمز</th>
                <th>الرمز النصي</th>
                <th>سعر الصرف</th>
                <th>الافتراضية</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @foreach($currencies as $currency)
                <tr>
                  <td>{{ $currency->id }}</td>
                  <td>{{ $currency->name }}</td>
                  <td>{{ $currency->code }}</td>
                  <td>{{ $currency->symbol }}</td>
                  <td>{{ $currency->exchange_rate }}</td>
                  <td>
                    @if($currency->is_default)
                      <span class="badge badge-success">نعم</span>
                    @else
                      <span class="badge badge-secondary">لا</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('currencies.show', $currency->id) }}" class="btn btn-sm btn-info">عرض</a>
                    <a href="{{ route('currencies.edit', $currency->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                    <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 