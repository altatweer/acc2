@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">تعديل السند رقم {{ $voucher->voucher_number }}</h1>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    @if($voucher->status == 'canceled')
      <div class="alert alert-danger text-center font-weight-bold">
        لا يمكن تعديل هذا السند لأنه ملغى (تم توليد قيد عكسي لإبطاله).<br>
        إذا كان هناك خطأ، يرجى إنشاء سند جديد بالقيم الصحيحة.
      </div>
    @else
      <div class="card">
        <div class="card-body">
          <form action="{{ route('vouchers.update', $voucher) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="type">نوع السند</label>
                <select name="type" id="type" class="form-control" required>
                  <option value="receipt" {{ $voucher->type=='receipt'?'selected':'' }}>قبض</option>
                  <option value="payment" {{ $voucher->type=='payment'?'selected':'' }}>صرف</option>
                  <option value="transfer" {{ $voucher->type=='transfer'?'selected':'' }}>تحويل</option>
                  <option value="deposit" {{ $voucher->type=='deposit'?'selected':'' }}>إيداع</option>
                  <option value="withdraw" {{ $voucher->type=='withdraw'?'selected':'' }}>سحب</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="date">التاريخ</label>
                <input type="datetime-local" name="date" id="date" class="form-control" value="{{ $voucher->date ? $voucher->date->format('Y-m-d\TH:i') : '' }}" required>
              </div>
              <div class="form-group col-md-3">
                <label for="currency">العملة</label>
                <select name="currency" id="currency" class="form-control" required>
                  @foreach($currencies as $cur)
                    <option value="{{ $cur->code }}" {{ $voucher->currency==$cur->code?'selected':'' }}>{{ $cur->code }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="recipient_name">اسم المستفيد</label>
                <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ $voucher->recipient_name }}">
              </div>
            </div>
            <div class="form-group">
              <label for="description">الوصف</label>
              <textarea name="description" id="description" class="form-control" rows="2">{{ $voucher->description }}</textarea>
            </div>
            <hr>
            <h5>المعاملات المالية:</h5>
            <div id="transactions-list">
              @foreach($voucher->transactions as $i => $tx)
              <div class="form-row transaction-row mb-2">
                <div class="form-group col-md-3">
                  <label>الحساب</label>
                  <input type="text" class="form-control" value="{{ $accounts->find($tx->account_id)->name ?? '-' }} ({{ $accounts->find($tx->account_id)->currency ?? '' }})" readonly>
                </div>
                <div class="form-group col-md-3">
                  <label>الحساب المستهدف</label>
                  <input type="text" class="form-control" value="{{ $accounts->find($tx->target_account_id)->name ?? '-' }} ({{ $accounts->find($tx->target_account_id)->currency ?? '' }})" readonly>
                </div>
                <div class="form-group col-md-2">
                  <label>المبلغ</label>
                  <input type="text" class="form-control" value="{{ number_format($tx->amount, 2) }}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>الوصف</label>
                  <input type="text" class="form-control" value="{{ $tx->description }}" readonly>
                </div>
              </div>
              @endforeach
            </div>
            <div class="form-group text-center">
              <button type="submit" class="btn btn-primary">تحديث السند</button>
              <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
          </form>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection 