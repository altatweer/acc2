@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">@lang('messages.edit_voucher_number', ['number' => $voucher->voucher_number])</h1>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    @if($voucher->status == 'canceled')
      <div class="alert alert-danger text-center font-weight-bold">
        @lang('messages.cannot_edit_canceled_voucher')
      </div>
    @else
      <div class="card">
        <div class="card-body">
          <form action="{{ Route::localizedRoute('vouchers.update', ['voucher' => $voucher, ]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="type">@lang('messages.voucher_type')</label>
                <select name="type" id="type" class="form-control" required>
                  <option value="receipt" {{ $voucher->type=='receipt'?'selected':'' }}>@lang('messages.receipt_voucher')</option>
                  <option value="payment" {{ $voucher->type=='payment'?'selected':'' }}>@lang('messages.payment_voucher')</option>
                  <option value="transfer" {{ $voucher->type=='transfer'?'selected':'' }}>@lang('messages.transfer_voucher')</option>
                  <option value="deposit" {{ $voucher->type=='deposit'?'selected':'' }}>@lang('messages.deposit')</option>
                  <option value="withdraw" {{ $voucher->type=='withdraw'?'selected':'' }}>@lang('messages.withdraw')</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="date">@lang('messages.voucher_date')</label>
                <input type="datetime-local" name="date" id="date" class="form-control" value="{{ $voucher->date ? $voucher->date->format('Y-m-d\TH:i') : '' }}" required>
              </div>
              <div class="form-group col-md-3">
                <label for="currency">@lang('messages.currency')</label>
                <select name="currency" id="currency" class="form-control" required>
                  @foreach($currencies as $cur)
                    <option value="{{ $cur->code }}" {{ $voucher->currency==$cur->code?'selected':'' }}>{{ $cur->code }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="recipient_name">@lang('messages.beneficiary_name')</label>
                <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ $voucher->recipient_name }}">
              </div>
            </div>
            <div class="form-group">
              <label for="description">@lang('messages.description')</label>
              <textarea name="description" id="description" class="form-control" rows="2">{{ $voucher->description }}</textarea>
            </div>
            <hr>
            <h5>@lang('messages.financial_transactions')</h5>
            <div id="transactions-list">
              @foreach($voucher->transactions as $i => $tx)
              <div class="form-row transaction-row mb-2">
                <div class="form-group col-md-3">
                  <label>@lang('messages.account')</label>
                  <input type="text" class="form-control" value="{{ $accounts->find($tx->account_id)->name ?? '-' }} ({{ $accounts->find($tx->account_id)->currency ?? '' }})" readonly>
                </div>
                <div class="form-group col-md-3">
                  <label>@lang('messages.target_account')</label>
                  <input type="text" class="form-control" value="{{ $accounts->find($tx->target_account_id)->name ?? '-' }} ({{ $accounts->find($tx->target_account_id)->currency ?? '' }})" readonly>
                </div>
                <div class="form-group col-md-2">
                  <label>@lang('messages.amount')</label>
                  <input type="text" class="form-control" value="{{ number_format($tx->amount, 2) }}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('messages.description')</label>
                  <input type="text" class="form-control" value="{{ $tx->description }}" readonly>
                </div>
              </div>
              @endforeach
            </div>
            <div class="form-group text-center">
              <button type="submit" class="btn btn-primary">@lang('messages.update_voucher')</button>
              <a href="{{ Route::localizedRoute('vouchers.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
            </div>
          </form>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection 