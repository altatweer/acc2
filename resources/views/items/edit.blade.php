@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">تعديل عنصر</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">تعديل بيانات العنصر</h3>
        </div>
        <form action="{{ route('items.update', $item) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="card-body">
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="form-group">
              <label>الاسم</label>
              <input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control" required>
            </div>
            <div class="form-group">
              <label>النوع</label>
              <select name="type" class="form-control" required>
                <option value="" disabled>-- اختر النوع --</option>
                <option value="product" {{ old('type', $item->type)=='product'?'selected':'' }}>منتج</option>
                <option value="service" {{ old('type', $item->type)=='service'?'selected':'' }}>خدمة</option>
              </select>
            </div>
            <div class="form-group">
              <label>سعر الوحدة</label>
              <input type="number" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label>الوصف</label>
              <textarea name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-warning">تحديث</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">إلغاء</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection 