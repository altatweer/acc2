@extends('layouts.print')

@section('print-content')
<div class="no-print print-actions text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> @lang('messages.print')
    </button>
</div>

<div class="document-title">
    <h3>@lang('messages.invoice_print_title', ['number' => $invoice->invoice_number])</h3>
</div>

<div class="document-info p-3 mb-4">
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.customer'):</strong>
            <span class="ml-2">{{ $invoice->customer->name }}</span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.date'):</strong>
            <span class="ml-2">{{ $invoice->date->format('Y-m-d') }}</span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.status'):</strong>
            @php
                $statusLabels = [
                    'draft'=>__('messages.invoice_status_draft'),
                    'unpaid'=>__('messages.invoice_status_unpaid'),
                    'partial'=>__('messages.invoice_status_partial'),
                    'paid'=>__('messages.invoice_status_paid')
                ];
                $badgeClass = $invoice->status=='draft' ? 'secondary' : ($invoice->status=='unpaid' ? 'warning' : ($invoice->status=='partial' ? 'info' : 'success'));
            @endphp
            <span class="badge badge-{{ $badgeClass }} ml-2">
                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
            </span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.currency'):</strong>
            <span class="ml-2">{{ $invoice->currency }}</span>
        </div>
    </div>
</div>

<div class="items-section mb-4">
    <h4 class="section-title">@lang('messages.invoice_line_items')</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-header">
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">@lang('messages.item')</th>
                    <th width="15%">@lang('messages.quantity')</th>
                    <th width="20%">@lang('messages.unit_price_short')</th>
                    <th width="20%">@lang('messages.line_total')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->item->name }} ({{ $item->item->type }})</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
                @if($invoice->invoiceItems->isEmpty())
                <tr>
                    <td colspan="5" class="text-center">@lang('messages.no_items_in_invoice')</td>
                </tr>
                @endif
                <tr class="table-total">
                    <td colspan="4" class="text-right"><strong>@lang('messages.total'):</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row summary-section mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="m-0">@lang('messages.payment_summary')</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-8"><strong>@lang('messages.total_amount'):</strong></div>
                    <div class="col-4 text-right">{{ number_format($invoice->total, 2) }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-8"><strong>@lang('messages.paid_amount'):</strong></div>
                    <div class="col-4 text-right text-success">{{ number_format($invoice->transactions()->where('type','receipt')->sum('amount'), 2) }}</div>
                </div>
                <div class="row">
                    <div class="col-8"><strong>@lang('messages.remaining_amount'):</strong></div>
                    <div class="col-4 text-right {{ $invoice->status == 'paid' ? 'text-success' : 'text-danger' }}">
                        {{ number_format($invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount'), 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="payments-section mb-4">
    <h4 class="section-title">@lang('messages.invoice_payments_section')</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-header">
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">@lang('messages.voucher_id')</th>
                    <th width="25%">@lang('messages.date')</th>
                    <th width="25%">@lang('messages.amount')</th>
                    <th width="25%">@lang('messages.currency')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $i=>$vch)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $vch->voucher_number }}</td>
                    <td>{{ $vch->date }}</td>
                    <td class="text-right">{{ number_format($vch->transactions->sum('amount'), 2) }}</td>
                    <td>{{ $vch->currency }}</td>
                </tr>
                @endforeach
                @if(count($payments)==0)
                <tr>
                    <td colspan="5" class="text-center">@lang('messages.no_payments_yet')</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="signature-section">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">@lang('messages.accountant_signature')</div>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">@lang('messages.customer_signature')</div>
    </div>
</div>

<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection 