@extends('layouts.print')

@section('print-content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="mb-4 text-center text-primary">@lang('messages.invoice_print_title', ['number' => $invoice->invoice_number])</h4>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th>@lang('messages.customer')</th>
                        <td>{{ $invoice->customer->name }}</td>
                        <th>@lang('messages.date')</th>
                        <td>{{ $invoice->date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.status')</th>
                        <td colspan="3">
                            @php
                                $statusLabels = [
                                    'draft'=>__('messages.invoice_status_draft'),
                                    'unpaid'=>__('messages.invoice_status_unpaid'),
                                    'partial'=>__('messages.invoice_status_partial'),
                                    'paid'=>__('messages.invoice_status_paid')
                                ];
                            @endphp
                            <span class="badge badge-{{ $invoice->status=='draft'?'secondary':($invoice->status=='unpaid'?'warning':($invoice->status=='partial'?'info':'success')) }}">
                                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </td>
                    </tr>
                </table>
                <h5 class="mb-3">@lang('messages.invoice_line_items')</h5>
                <table class="table table-bordered table-striped text-center">
                    <thead class="thead-light">
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
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>@lang('messages.total'):</h5>
                        <div class="alert alert-info font-weight-bold" style="font-size:1.2em;">
                            {{ number_format($invoice->total,2) }} {{ $invoice->currency }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>@lang('messages.paid_amount'):</h5>
                        <div class="alert alert-success font-weight-bold" style="font-size:1.2em;">
                            {{ number_format($invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}
                        </div>
                        <h5>@lang('messages.remaining_amount'):</h5>
                        <div class="alert alert-warning font-weight-bold" style="font-size:1.2em;">
                            {{ number_format($invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}
                        </div>
                    </div>
                </div>
                <h5 class="mb-3 mt-4">@lang('messages.invoice_payments_section')</h5>
                <table class="table table-bordered table-striped text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>@lang('messages.item_hash')</th>
                            <th>@lang('messages.voucher_id')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('messages.amount')</th>
                            <th>@lang('messages.currency')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $i=>$vch)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $vch->voucher_number }}</td>
                            <td>{{ $vch->date }}</td>
                            <td>{{ number_format($vch->transactions->sum('amount'),2) }}</td>
                            <td>{{ $vch->currency }}</td>
                        </tr>
                        @endforeach
                        @if(count($payments)==0)
                        <tr><td colspan="5" class="text-center">@lang('messages.no_payments_yet')</td></tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-5 row">
                    <div class="col text-center">
                        <span class="d-inline-block border-top pt-2 px-4">@lang('messages.customer_signature')</span>
                    </div>
                    <div class="col text-center">
                        <span class="d-inline-block border-top pt-2 px-4">@lang('messages.accountant_signature')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection 