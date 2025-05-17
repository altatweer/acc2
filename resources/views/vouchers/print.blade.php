@extends('layouts.print')

@section('print-content')
<div class="no-print print-actions text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> @lang('messages.print')
    </button>
</div>

<div class="document-title">
    <h3>@lang('messages.financial_voucher_number', ['number' => $voucher->voucher_number])</h3>
</div>

<div class="document-info p-3 mb-4">
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.voucher_type'):</strong>
            <span class="badge badge-{{ $voucher->type == 'receipt' ? 'success' : ($voucher->type == 'payment' ? 'danger' : 'info') }} ml-2">
                @if($voucher->type == 'receipt')
                    @lang('messages.receipt')
                @elseif($voucher->type == 'payment')
                    @lang('messages.payment')
                @else
                    @lang('messages.transfer')
                @endif
            </span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.voucher_date'):</strong>
            <span>{{ $voucher->date ? \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i') : '-' }}</span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.accountant'):</strong>
            <span>{{ $voucher->user->name ?? '-' }}</span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.recipient_payer'):</strong>
            <span>{{ $voucher->recipient_name }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <strong>@lang('messages.description'):</strong>
            <span>{{ $voucher->description }}</span>
        </div>
    </div>
</div>

<div class="transactions-section mb-4">
    <h4 class="section-title">@lang('messages.related_financial_transactions')</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-header">
                <tr>
                    <th>@lang('messages.account')</th>
                    <th>@lang('messages.debit')</th>
                    <th>@lang('messages.credit')</th>
                    <th>@lang('messages.currency')</th>
                    <th>@lang('messages.description')</th>
                </tr>
            </thead>
            <tbody>
                @if($voucher->journalEntry && $voucher->journalEntry->lines && $voucher->journalEntry->lines->count())
                    @php 
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp
                    @foreach($voucher->journalEntry->lines as $line)
                        @php 
                            $totalDebit += $line->debit;
                            $totalCredit += $line->credit;
                        @endphp
                        <tr>
                            <td>{{ $line->account->name ?? '-' }}</td>
                            <td class="text-right">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                            <td class="text-right">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            <td>{{ $line->currency }}</td>
                            <td>{{ $line->description }}</td>
                        </tr>
                    @endforeach
                    
                    @if($voucher->type == 'transfer')
                    <!-- عرض الإجماليات لسند التحويل: إظهار كل مبلغ على حدة -->
                    <tr class="table-info">
                        <td colspan="5" class="text-center font-weight-bold border-bottom">@lang('messages.totals')</td>
                    </tr>
                    @foreach($voucher->journalEntry->lines->groupBy('currency') as $currency => $lines)
                        @php
                            $currDebit = $lines->sum('debit');
                            $currCredit = $lines->sum('credit');
                        @endphp
                        <tr class="table-total">
                            <td><strong>{{ $currency }} @lang('messages.total')</strong></td>
                            <td class="text-right"><strong>{{ number_format($currDebit, 2) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($currCredit, 2) }}</strong></td>
                            <td>{{ $currency }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    @else
                    <!-- عرض الإجماليات لسندات القبض والصرف: مبلغ واحد فقط -->
                    <tr class="table-total">
                        <td><strong>@lang('messages.total')</strong></td>
                        <td colspan="2" class="text-center font-weight-bold">
                            {{ number_format($totalDebit, 2) }} {{ $voucher->journalEntry->lines->first()->currency ?? '-' }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="5" class="text-center">@lang('messages.no_financial_transactions')</td>
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
        <div class="signature-title">@lang('messages.recipient_signature')</div>
    </div>
</div>

<style>
/* تحسين مظهر الطباعة */
.table-total { background-color: #f8f9fa; }
.table-info { background-color: #e3f2fd; }
.badge { font-size: 90%; padding: 5px 10px; }

@media print {
    /* Ocultar específicamente el encabezado "تطوير" y otros elementos no deseados en la impresión */
    h1:first-child, 
    h1:first-of-type, 
    .header-content, 
    .page-title,
    .app-header,
    .app-page-header,
    .navbar-brand {
        display: none !important;
    }
    
    /* Ocultar específicamente "AurSuite" y la fecha en la parte superior */
    #print-header,
    .print-page-title,
    body > div:first-child > h1,
    body > div:first-child > div:first-child,
    div:contains('AurSuite'),
    div:contains('messages.print_date'),
    header, 
    .pdf-header {
        display: none !important;
        height: 0 !important;
        visibility: hidden !important;
    }
    
    /* تحسين مظهر الطباعة */
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #ddd; }
    .table-total { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    .table-info { background-color: #e3f2fd !important; -webkit-print-color-adjust: exact; }
    
    /* Configuración de página para eliminar encabezados y pies de página */
    @page {
        size: auto;
        margin: 0mm;
        margin-top: 0;
        margin-header: 0 !important;
        margin-footer: 0 !important;
    }
}
</style>

<script>
    window.onload = function() {
        // Ocultar encabezados antes de imprimir
        var elementsToHide = document.querySelectorAll('h1, header, .header');
        elementsToHide.forEach(function(element) {
            element.style.display = 'none';
        });
        
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection 