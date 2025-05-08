@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">@lang('messages.invoices_management_title')</h1>
      </div>
      <div class="col-sm-6 text-left">
        @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
        @if($isSuperAdmin || auth()->user()->can('إضافة فاتورة'))
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">@lang('messages.create_invoice')</a>
        @endif
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">@lang('messages.invoices_list_title')</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>@lang('messages.item_hash')</th>
              <th>@lang('messages.invoice_id')</th>
              <th>@lang('messages.customer')</th>
              <th>@lang('messages.date')</th>
              <th>@lang('messages.total')</th>
              <th>@lang('messages.currency')</th>
              <th>@lang('messages.status')</th>
              <th>@lang('messages.actions')</th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoices as $inv)
            <tr>
              <td>{{ $invoices->firstItem() + $loop->index }}</td>
              <td>{{ $inv->invoice_number }}</td>
              <td>{{ $inv->customer->name }}</td>
              <td>{{ $inv->date->format('Y-m-d') }}</td>
              <td>{{ number_format($inv->total,2) }}</td>
              <td>{{ $inv->currency }}</td>
              <td>
                @php
                  $statusLabels = [
                      'draft'=>__('messages.status_draft'),
                      'unpaid'=>__('messages.status_unpaid'),
                      'partial'=>__('messages.status_partial'),
                      'paid'=>__('messages.status_paid')
                  ];
                  $badgeClass = $inv->status=='draft' ? 'secondary' : ($inv->status=='unpaid' ? 'warning' : ($inv->status=='partial' ? 'info' : 'success'));
                @endphp
                <span class="badge badge-{{ $badgeClass }}">
                  {{ $statusLabels[$inv->status] ?? $inv->status }}
                </span>
              </td>
              <td>
                @if($isSuperAdmin || auth()->user()->can('عرض الفواتير'))
                <a href="{{ Route::localizedRoute('invoices.show', ['invoice' => $inv, ]) }}" class="btn btn-sm btn-info">@lang('messages.view')</a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-between align-items-center">
        <div>@lang('messages.total_invoices_count') <strong>{{ $invoices->total() }}</strong></div>
        <div>{{ $invoices->appends(['lang' => app()->getLocale()])->links() }}</div>
      </div>
    </div>
  </div>
</section>
@endsection 