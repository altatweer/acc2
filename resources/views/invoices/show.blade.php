@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">تفاصيل الفاتورة {{ $invoice->invoice_number }}</h1>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <h5>بيانات الفاتورة:</h5>
          <table class="table table-bordered">
            <tr><th>رقم الفاتورة</th><td>{{ $invoice->invoice_number }}</td></tr>
            <tr><th>العميل</th><td>{{ $invoice->customer->name }}</td></tr>
            <tr><th>التاريخ</th><td>{{ $invoice->date->format('Y-m-d') }}</td></tr>
            <tr><th>الإجمالي</th><td>{{ number_format($invoice->total,2) }} {{ $invoice->currency }}</td></tr>
            <tr><th>الحالة</th><td>{{ ['unpaid'=>'غير مدفوعة','partial'=>'مدفوعة جزئي','paid'=>'مدفوعة'][$invoice->status] }}</td></tr>
          </table>

          <hr>
          <h5>بنود الفاتورة:</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>الصنف</th>
                  <th>الكمية</th>
                  <th>سعر الوحدة</th>
                  <th>الإجمالي</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoice->invoiceItems as $i => $item)
                <tr>
                  <td>{{ $i+1 }}</td>
                  <td>{{ $item->item->name }} ({{ $item->item->type }})</td>
                  <td>{{ $item->quantity }}</td>
                  <td>{{ number_format($item->unit_price,2) }}</td>
                  <td>{{ number_format($item->line_total,2) }}</td>
                </tr>
                @endforeach
                @if($invoice->invoiceItems->isEmpty())
                <tr><td colspan="5" class="text-center">لا توجد بنود في هذه الفاتورة.</td></tr>
                @endif
              </tbody>
            </table>
          </div>

          <hr>
          <h5>دفعات سابقة:</h5>
          <table class="table table-bordered table-striped">
            <thead><tr><th>#</th><th>رقم السند</th><th>التاريخ</th><th>المبلغ</th><th>الإجراءات</th></tr></thead>
            <tbody>
              @foreach($payments as $i=>$vch)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $vch->voucher_number }}</td>
                <td>{{ $vch->date }}</td>
                <td>{{ number_format($vch->transactions->sum('amount'),2) }} {{ $vch->currency }}</td>
                <td><a href="{{ route('vouchers.show',$vch) }}" class="btn btn-sm btn-info">عرض السند</a></td>
              </tr>
              @endforeach
              @if(count($payments)==0)
                <tr><td colspan="5" class="text-center">لم يتم سداد أي دفعات بعد.</td></tr>
              @endif
            </tbody>
          </table>

          @if($invoice->status!='paid')
          <hr>
          <h5>سداد جديد:</h5>
          <form action="{{ route('invoice-payments.store') }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="cash_account_id">صندوق الدفع</label>
                <select name="cash_account_id" id="cash_account_id" class="form-control select2" required>
                  @foreach($cashAccounts as $acc)
                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}">{{ $acc->name }} ({{ $acc->currency }})</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="payment_amount">المبلغ المدفوع ({{ $invoice->currency }})</label>
                <input type="number" name="payment_amount" id="payment_amount" value="{{ old('payment_amount', $invoice->total) }}" class="form-control" step="0.01" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="exchange_rate">سعر الصرف</label>
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" step="0.000001" value="{{ $invoice->exchange_rate }}" readonly>
              </div>
              <div class="form-group col-md-6">
                <label for="date">تاريخ السداد</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            <button type="submit" class="btn btn-success">سداد</button>
          </form>
          @endif

        </div>
      </div>
    </div>
  </section>
</div>
@endsection 