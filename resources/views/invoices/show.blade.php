@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">@lang('messages.invoice_details', ['number' => $invoice->invoice_number])</h1>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <h5>@lang('messages.invoice_info')</h5>
        <table class="table table-bordered">
          <tr><th>@lang('messages.invoice_id')</th><td>{{ $invoice->invoice_number }}</td></tr>
          <tr><th>@lang('messages.customer')</th><td>{{ $invoice->customer->name }}</td></tr>
          <tr><th>@lang('messages.date')</th><td>{{ $invoice->date->format('Y-m-d') }}</td></tr>
          <tr><th>@lang('messages.total')</th><td>{{ number_format($invoice->total,2) }} {{ $invoice->currency }}</td></tr>
          <tr><th>@lang('messages.status')</th><td>
            @php
              $statusLabels = [
                'draft'=>__('messages.status_draft'),
                'unpaid'=>__('messages.status_unpaid'),
                'partial'=>__('messages.status_partial'),
                'paid'=>__('messages.status_paid')
              ];
            @endphp
            <span class="badge badge-{{ $invoice->status=='draft'?'secondary':($invoice->status=='unpaid'?'warning':($invoice->status=='partial'?'info':'success')) }}">
              {{ $statusLabels[$invoice->status] ?? $invoice->status }}
            </span>
          </td></tr>
          <tr><th>@lang('messages.paid_amount')</th><td>{{ number_format($invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}</td></tr>
          <tr><th>@lang('messages.remaining_amount')</th><td>{{ number_format($invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}</td></tr>
        </table>

        <hr>
        <h5>@lang('messages.invoice_line_items')</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>@lang('messages.item_hash')</th>
                <th>@lang('messages.item')</th>
                <th>@lang('messages.quantity')</th>
                <th>@lang('messages.unit_price_short')</th>
                <th>@lang('messages.line_total')</th>
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
              <tr><td colspan="5" class="text-center">@lang('messages.no_items_in_invoice')</td></tr>
              @endif
            </tbody>
          </table>
        </div>

        <hr>
        <h5>@lang('messages.previous_payments')</h5>
        <table class="table table-bordered table-striped">
          <thead><tr><th>@lang('messages.item_hash')</th><th>@lang('messages.voucher_id')</th><th>@lang('messages.date')</th><th>@lang('messages.amount')</th><th>@lang('messages.actions')</th></tr></thead>
          <tbody>
            @foreach($payments as $i=>$vch)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $vch->voucher_number }}</td>
              <td>{{ $vch->date }}</td>
              <td>{{ number_format($vch->transactions->sum('amount'),2) }} {{ $vch->currency }}</td>
              <td><a href="{{ Route::localizedRoute('vouchers.show', ['voucher' => $vch->id]) }}" class="btn btn-sm btn-info">@lang('messages.view_voucher')</a></td>
            </tr>
            @endforeach
            @if(count($payments)==0)
              <tr><td colspan="5" class="text-center">@lang('messages.no_payments_yet')</td></tr>
            @endif
          </tbody>
        </table>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($invoice->status=='paid')
        <div class="alert alert-success">@lang('messages.invoice_paid_fully_no_new_payments')</div>
        @endif

        @if(in_array($invoice->status, ['unpaid','partial']))
        <hr>
        <h5>@lang('messages.new_payment')</h5>
        <form action="{{ Route::localizedRoute('invoice-payments.store') }}" method="POST" class="mt-3" id="paymentForm">
          @csrf
          <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="cash_account_id">@lang('messages.payment_cash_account')</label>
              <select name="cash_account_id" id="cash_account_id" class="form-control select2" required>
                @foreach($cashAccounts as $acc)
                  <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}">{{ $acc->name }} ({{ $acc->currency }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="payment_amount">@lang('messages.payment_amount_currency', ['currency' => $invoice->currency])</label>
              <input type="number" name="payment_amount" id="payment_amount" value="{{ old('payment_amount', $invoice->total) }}" class="form-control" step="0.01" required>
              <small id="amountWarning" class="text-danger d-none">@lang('messages.payment_amount_exceeds_remaining')</small>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="exchange_rate">@lang('messages.exchange_rate')</label>
              <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" step="0.000001" value="{{ $invoice->exchange_rate }}" readonly>
            </div>
            <div class="form-group col-md-6">
              <label for="date">@lang('messages.payment_date')</label>
              <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          <button type="submit" class="btn btn-success">@lang('messages.pay_button')</button>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          var paymentInput = document.getElementById('payment_amount');
          var warning = document.getElementById('amountWarning');
          var form = document.getElementById('paymentForm');
          var max = {{ $invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount') }};
          paymentInput.addEventListener('input', function() {
            if (parseFloat(paymentInput.value) > max) {
              warning.classList.remove('d-none');
            } else {
              warning.classList.add('d-none');
            }
          });
          form.addEventListener('submit', function(e) {
            if (parseFloat(paymentInput.value) > max) {
              e.preventDefault();
              warning.classList.remove('d-none');
              paymentInput.focus();
            }
          });
        });
        </script>
        @endif

        <div class="mt-4 mb-2">
          @if($invoice->status=='draft')
            <form action="{{ Route::localizedRoute('invoices.approve', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              <button type="submit" class="btn btn-success">@lang('messages.approve_invoice')</button>
            </form>
            <a href="{{ Route::localizedRoute('invoices.edit', ['invoice' => $invoice, ]) }}" class="btn btn-primary">@lang('messages.edit')</a>
            <form action="{{ Route::localizedRoute('invoices.destroy', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.delete_invoice_confirm')')">@lang('messages.delete')</button>
            </form>
          @elseif($invoice->status=='unpaid')
            <form action="{{ Route::localizedRoute('invoices.cancel', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.cancel_invoice_confirm')')">@lang('messages.cancel_invoice')</button>
            </form>
          @elseif($invoice->status=='partial')
            <div class="alert alert-info">@lang('messages.cannot_cancel_partial_payments')</div>
          @elseif($invoice->status=='paid')
            <div class="alert alert-success">@lang('messages.invoice_paid_cannot_edit_cancel')</div>
          @endif
        </div>

        <div class="mb-3 text-center">
            <a href="{{ Route::localizedRoute('invoices.print', ['invoice' => $invoice, ]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> @lang('messages.print_invoice')</a>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection 