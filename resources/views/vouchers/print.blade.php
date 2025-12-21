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

<div class="document-info">
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>@lang('messages.voucher_type'):</strong>
            <span class="badge badge-{{ $voucher->type == 'receipt' ? 'success' : ($voucher->type == 'payment' ? 'danger' : 'info') }} mr-2">
                @if($voucher->type == 'receipt')
                    <i class="fas fa-arrow-down"></i> @lang('messages.receipt')
                @elseif($voucher->type == 'payment')
                    <i class="fas fa-arrow-up"></i> @lang('messages.payment')
                @else
                    <i class="fas fa-exchange-alt"></i> @lang('messages.transfer')
                @endif
            </span>
        </div>
        <div class="col-md-6">
            <strong>@lang('messages.voucher_date'):</strong>
            <span class="currency-amount">{{ $voucher->date ? \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i') : '-' }}</span>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>@lang('messages.accountant'):</strong>
            <span>{{ $voucher->user->name ?? '-' }}</span>
        </div>
        <div class="col-md-6">
            <strong>@lang('messages.recipient_payer'):</strong>
            <span>{{ $voucher->recipient_name }}</span>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>العملة:</strong>
            <span class="badge badge-info">{{ $voucher->currency ?? 'USD' }}</span>
        </div>
        <div class="col-md-6">
            <strong>سعر الصرف:</strong>
            <span class="currency-amount">{{ number_format($voucher->exchange_rate ?? 1, 4) }}</span>
        </div>
    </div>
    
    @if($voucher->description)
    <div class="row">
        <div class="col-12">
            <strong>@lang('messages.description'):</strong>
            <div class="mt-2 p-2 bg-light border-right-4 border-primary rounded">
                {{ $voucher->description }}
            </div>
        </div>
    </div>
    @endif
</div>

<div class="transactions-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-list-alt text-primary"></i> @lang('messages.related_financial_transactions')
    </h4>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">@lang('messages.account')</th>
                    <th width="15%">@lang('messages.debit')</th>
                    <th width="15%">@lang('messages.credit')</th>
                    <th width="10%">@lang('messages.currency')</th>
                    <th width="10%">سعر الصرف</th>
                    <th width="20%">@lang('messages.description')</th>
                </tr>
            </thead>
            <tbody>
                @if($voucher->journalEntry && $voucher->journalEntry->lines && $voucher->journalEntry->lines->count())
                    @php 
                        $totalDebit = 0;
                        $totalCredit = 0;
                        $index = 1;
                    @endphp
                    @foreach($voucher->journalEntry->lines as $line)
                        @php 
                            $totalDebit += $line->debit;
                            $totalCredit += $line->credit;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index++ }}</td>
                            <td>
                                <strong>{{ $line->account->name ?? '-' }}</strong>
                                @if($line->account->code)
                                    <br><small class="text-muted">رمز الحساب: {{ $line->account->code }}</small>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($line->debit > 0)
                                    <span class="text-debit currency-amount">{{ number_format($line->debit, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($line->credit > 0)
                                    <span class="text-credit currency-amount">{{ number_format($line->credit, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $line->currency }}</span>
                            </td>
                            <td class="text-center">
                                @if($line->exchange_rate && $line->exchange_rate != 1.0)
                                    <strong class="text-info">{{ number_format($line->exchange_rate, 4) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $line->description ?? '-' }}</small>
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- Totals Section -->
                    @if($voucher->type == 'transfer')
                        <!-- Multi-currency transfer totals -->
                        <tr class="table-info">
                            <td colspan="7" class="text-center font-weight-bold">
                                <i class="fas fa-calculator text-primary"></i> @lang('messages.totals')
                            </td>
                        </tr>
                        @foreach($voucher->journalEntry->lines->groupBy('currency') as $currency => $lines)
                            @php
                                $currDebit = $lines->sum('debit');
                                $currCredit = $lines->sum('credit');
                            @endphp
                            <tr class="table-total">
                                <td class="text-center"><i class="fas fa-coins text-warning"></i></td>
                                <td><strong>إجمالي {{ $currency }}</strong></td>
                                <td class="text-right">
                                    <strong class="text-debit currency-amount">{{ number_format($currDebit, 2) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong class="text-credit currency-amount">{{ number_format($currCredit, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $currency }}</span>
                                </td>
                                <td class="text-center">-</td>
                                <td class="text-center">
                                    @if($currDebit == $currCredit)
                                        <i class="fas fa-check-circle text-success" title="متوازن"></i>
                                    @else
                                        <i class="fas fa-exclamation-triangle text-warning" title="غير متوازن"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Single currency totals -->
                        <tr class="table-total">
                            <td class="text-center"><i class="fas fa-calculator text-primary"></i></td>
                            <td><strong>@lang('messages.total')</strong></td>
                            <td class="text-right">
                                <strong class="text-debit currency-amount">{{ number_format($totalDebit, 2) }}</strong>
                            </td>
                            <td class="text-right">
                                <strong class="text-credit currency-amount">{{ number_format($totalCredit, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $voucher->journalEntry->lines->first()->currency ?? '-' }}</span>
                            </td>
                            <td class="text-center">-</td>
                            <td class="text-center">
                                @if($totalDebit == $totalCredit)
                                    <i class="fas fa-check-circle text-success" title="متوازن"></i>
                                @else
                                    <i class="fas fa-exclamation-triangle text-warning" title="غير متوازن"></i>
                                @endif
                            </td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-info-circle text-muted"></i>
                            @lang('messages.no_financial_transactions')
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Voucher Status -->
<div class="voucher-status mb-4">
    <div class="alert alert-{{ $voucher->status === 'active' ? 'success' : 'danger' }} text-center">
        <h5 class="mb-0">
            <i class="fas fa-{{ $voucher->status === 'active' ? 'check-circle' : 'ban' }}"></i>
            حالة السند: 
            @if($voucher->status === 'active')
                <strong>نشط ومعتمد</strong>
            @else
                <strong>ملغى</strong>
            @endif
        </h5>
    </div>
</div>

<!-- Signature Section -->
<div class="signature-section page-break-inside-avoid">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">توقيع المحاسب</div>
        <div class="signature-name">{{ $voucher->user->name ?? '' }}</div>
    </div>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">توقيع المستلم/الدافع</div>
        <div class="signature-name">{{ $voucher->recipient_name ?? '' }}</div>
    </div>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">الختم الرسمي</div>
        <div class="signature-name"></div>
    </div>
</div>

<!-- Additional Styles -->
<style>
.section-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.voucher-status .alert {
    border: 2px solid;
    border-radius: 10px;
    font-size: 16px;
}

.signature-name {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    min-height: 15px;
}

.border-right-4 {
    border-right: 4px solid #3498db !important;
}

@media print {
    .section-title {
        font-size: 16px;
        margin-bottom: 15px;
        padding-bottom: 8px;
    }
    
    .voucher-status .alert {
        font-size: 14px;
        padding: 10px;
    }
    
    .signature-section {
        margin-top: 40mm;
    }
    
    .signature-box {
        min-height: 25mm;
    }
    
    .signature-line {
        margin: 20mm 0 5mm 0;
    }
    
    /* Ensure proper page breaks */
    .transactions-section {
        page-break-inside: avoid;
    }
    
    .table thead {
        display: table-header-group;
    }
    
    .table tbody {
        display: table-row-group;
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