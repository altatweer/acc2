@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">@lang('messages.view_customer')</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="card mt-3">
        <div class="card-body">
          <table class="table table-bordered">
            <tr><th>#</th><td>{{ $customer->id }}</td></tr>
            <tr><th>@lang('messages.customer_name')</th><td>{{ $customer->name }}</td></tr>
            <tr><th>@lang('messages.customer_email')</th><td>{{ $customer->email }}</td></tr>
            <tr><th>@lang('messages.customer_phone')</th><td>{{ $customer->phone ?? '-' }}</td></tr>
            <tr><th>@lang('messages.customer_address')</th><td>{{ $customer->address ?? '-' }}</td></tr>
            <tr><th>@lang('messages.receivables_account')</th><td>{{ $customer->account->name }}</td></tr>
          </table>
        </div>
        <div class="card-footer text-right">
          <a href="{{ Route::localizedRoute('customers.edit', ['customer' => $customer, ]) }}" class="btn btn-sm btn-warning">@lang('messages.edit')</a>
          <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">@lang('messages.back')</a>
        </div>
      </div>
      <div class="mb-4">
        <h5 class="mb-2">@lang('messages.customer_balance'):</h5>
        <div class="alert alert-info font-weight-bold" style="font-size:1.2em;">
          {{ number_format($balance, 2) }}
        </div>
      </div>
      <div>
        <h5 class="mb-3">@lang('messages.customer_invoices')</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped text-center">
            <thead>
              <tr>
                <th>#</th>
                <th>@lang('messages.invoice_number')</th>
                <th>@lang('messages.invoice_date')</th>
                <th>@lang('messages.invoice_total')</th>
                <th>@lang('messages.invoice_status')</th>
                <th>@lang('messages.actions')</th>
              </tr>
            </thead>
            <tbody>
              @forelse($invoices as $i => $invoice)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->date ? $invoice->date->format('Y-m-d') : '-' }}</td>
                <td>{{ number_format($invoice->total, 2) }}</td>
                <td>{{ __($invoice->status) }}</td>
                <td>
                  <a href="{{ Route::localizedRoute('invoices.show', ['invoice' => $invoice, ]) }}" class="btn btn-sm btn-outline-info" title="@lang('messages.view_invoice')"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
              @empty
              <tr><td colspan="6">@lang('messages.no_invoices_for_customer')</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection 