@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">عميل جديد</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">إضافة عميل جديد</h3>
        </div>
        <form action="{{ route('customers.store') }}" method="POST">
          @csrf
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
              <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="form-group">
              <label>البريد الإلكتروني</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>
            <div class="form-group">
              <label>الهاتف</label>
              <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
            <div class="form-group">
              <label for="address">العنوان</label>
              <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">حفظ</button>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">إلغاء</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(function(){ $('.select2').select2({ theme:'bootstrap4' }); });</script>
@endpush 