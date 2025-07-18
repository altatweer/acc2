@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">@lang('messages.edit_customer')</h1>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card card-warning">
      <div class="card-header">
        <h3 class="card-title">@lang('messages.customer_data')</h3>
      </div>
      <form action="{{ Route::localizedRoute('customers.update', ['customer' => $customer, ]) }}" method="POST">
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
            <label>@lang('messages.customer_name')</label>
            <input type="text" name="name" value="{{ old('name',$customer->name) }}" class="form-control" required>
          </div>
          <div class="form-group">
            <label>@lang('messages.customer_email')</label>
            <input type="email" name="email" value="{{ old('email',$customer->email) }}" class="form-control" required>
          </div>
          <div class="form-group">
            <label>@lang('messages.customer_phone')</label>
            <input type="text" name="phone" value="{{ old('phone',$customer->phone) }}" class="form-control">
          </div>
          <div class="form-group">
            <label>@lang('messages.customer_address')</label>
            <textarea name="address" class="form-control">{{ old('address',$customer->address) }}</textarea>
          </div>
          <div class="form-group">
            <label>@lang('messages.receivables_account')</label>
            <div class="input-group">
              <input type="text" class="form-control" 
                     value="{{ $customer->account ? $customer->account->name . ' (' . $customer->account->code . ')' : 'لا يوجد حساب' }}" 
                     readonly 
                     style="background-color: #f8f9fa; cursor: not-allowed;">
              <div class="input-group-append">
                <span class="input-group-text bg-secondary text-white">
                  <i class="fas fa-lock"></i>
                </span>
              </div>
            </div>
            <small class="form-text text-muted">
              <i class="fas fa-info-circle"></i>
              الحساب المحاسبي مرتبط تلقائياً بالعميل ولا يمكن تعديله لحماية البيانات المحاسبية
            </small>
            <!-- إخفاء الحساب المحاسبي الحالي للحفاظ عليه -->
            <input type="hidden" name="account_id" value="{{ $customer->account_id }}">
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-warning">@lang('messages.update')</button>
          <a href="{{ route('customers.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(function(){ $('.select2').select2({ theme:'bootstrap4' }); });</script>
@endpush 