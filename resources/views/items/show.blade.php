@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">عرض بيانات العنصر</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr><th>#</th><td>{{ $item->id }}</td></tr>
            <tr><th>الاسم</th><td>{{ $item->name }}</td></tr>
            <tr><th>النوع</th><td>{{ $item->type == 'product' ? 'منتج' : 'خدمة' }}</td></tr>
            <tr><th>سعر الوحدة</th><td>{{ number_format($item->unit_price,2) }}</td></tr>
            <tr><th>الوصف</th><td>{{ $item->description ?? '-' }}</td></tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning">تعديل</a>
          <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary">رجوع</a>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 