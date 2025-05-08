@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">@lang('messages.pay_invoice')</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-info">
        <div class="card-header"><h3 class="card-title">@lang('messages.payment_information')</h3></div>
        <form action="{{ route('invoice-payments.store') }}" method="POST">
          @csrf

          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
              <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="form-group">
              <label>@lang('messages.select_invoice')</label>
              <select name="invoice_id" id="invoice_id" class="form-control select2" required>
                <option value="" disabled selected>-- @lang('messages.choose_invoice') --</option>
                @foreach($invoices as $inv)
                  <option value="{{ $inv->id }}" data-total="{{ $inv->total }}" data-currency="{{ $inv->currency }}" data-exchange_rate="{{ $inv->exchange_rate }}">
                    {{ $inv->invoice_number }} | {{ $inv->customer->name }} | {{ $inv->total }} {{ $inv->currency }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>@lang('messages.payment_cashbox')</label>
                <select name="cash_account_id" id="cash_account_id" class="form-control select2" required>
                  <option value="" disabled selected>-- @lang('messages.choose_cashbox') --</option>
                  @foreach($cashAccounts as $acc)
                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}">{{ $acc->name }} ({{ $acc->currency }})</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-6">
                <label>@lang('messages.payment_amount')</label>
                <input type="number" name="payment_amount" id="payment_amount" class="form-control" step="0.01" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>@lang('messages.exchange_rate')</label>
                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" step="0.000001" readonly>
              </div>
              <div class="form-group col-md-6">
                <label>@lang('messages.payment_date')</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
          </div>

          <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">@lang('messages.pay_button')</button>
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
<script>
$(function(){
    $('.select2').select2({ theme: 'bootstrap4' });
    // On invoice selection, filter cash accounts and fill amount and exchange rate
    $('#invoice_id').on('change', function(){
        var data = $(this).find('option:selected').data();
        // fill payment amount and exchange rate from invoice
        $('#payment_amount').val(data.total);
        $('#exchange_rate').val(data.exchange_rate);
        // filter cash accounts by invoice currency
        $('#cash_account_id option').each(function(){
            $(this).toggle($(this).data('currency') === data.currency);
        });
        // select first visible cash account
        var first = $('#cash_account_id option:visible').first().val();
        $('#cash_account_id').val(first);
    }).trigger('change');
});
</script>
@endpush 