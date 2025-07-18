@extends('layouts.print')

@section('print-content')
<div class="no-print print-actions text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> @lang('messages.print')
    </button>
</div>

<div class="document-title">
    <h3>فاتورة رقم {{ $invoice->invoice_number }}</h3>
</div>

<div class="document-info">
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>نوع الفاتورة:</strong>
            <span class="badge badge-{{ $invoice->type == 'sales' ? 'success' : 'info' }}">
                @if($invoice->type == 'sales')
                    <i class="fas fa-shopping-cart"></i> فاتورة مبيعات
                @else
                    <i class="fas fa-file-invoice"></i> فاتورة خدمات
                @endif
            </span>
        </div>
        <div class="col-md-6">
            <strong>تاريخ الفاتورة:</strong>
            <span class="currency-amount">{{ $invoice->date ? \Illuminate\Support\Carbon::parse($invoice->date)->format('Y-m-d') : '-' }}</span>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>العميل:</strong>
            <div class="customer-info">
                <div class="customer-name">{{ $invoice->customer->name ?? 'عميل نقدي' }}</div>
                @if($invoice->customer && $invoice->customer->phone)
                    <div class="customer-contact">
                        <i class="fas fa-phone"></i> {{ $invoice->customer->phone }}
                    </div>
                @endif
                @if($invoice->customer && $invoice->customer->email)
                    <div class="customer-contact">
                        <i class="fas fa-envelope"></i> {{ $invoice->customer->email }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <strong>حالة الفاتورة:</strong>
            <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partial' ? 'warning' : 'danger') }}">
                @if($invoice->status == 'paid')
                    <i class="fas fa-check-circle"></i> مدفوعة
                @elseif($invoice->status == 'partial')
                    <i class="fas fa-clock"></i> مدفوعة جزئياً
                @else
                    <i class="fas fa-exclamation-circle"></i> غير مدفوعة
                @endif
            </span>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>العملة:</strong>
            <span class="badge badge-info">{{ $invoice->currency ?? 'USD' }}</span>
        </div>
        <div class="col-md-6">
            <strong>سعر الصرف:</strong>
            <span class="currency-amount">{{ number_format($invoice->exchange_rate ?? 1, 4) }}</span>
        </div>
    </div>
    
    @if($invoice->notes)
    <div class="row">
        <div class="col-12">
            <strong>ملاحظات:</strong>
            <div class="mt-2 p-2 bg-light border-right-4 border-primary rounded">
                {{ $invoice->notes }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Invoice Items Section -->
<div class="items-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-list text-primary"></i> تفاصيل الفاتورة
    </h4>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">الصنف</th>
                    <th width="15%">الكمية</th>
                    <th width="15%">سعر الوحدة</th>
                    <th width="15%">الخصم</th>
                    <th width="20%">المجموع</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $subtotal = 0;
                    $totalDiscount = 0;
                    $index = 1;
                @endphp
                @foreach($invoice->invoiceItems as $item)
                    @php 
                        $lineTotal = $item->quantity * $item->unit_price;
                        $lineDiscount = $item->discount ?? 0;
                        $lineFinal = $lineTotal - $lineDiscount;
                        $subtotal += $lineFinal;
                        $totalDiscount += $lineDiscount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index++ }}</td>
                        <td>
                            <strong>{{ $item->item->name ?? $item->description }}</strong>
                            @if($item->item && $item->item->code)
                                <br><small class="text-muted">كود الصنف: {{ $item->item->code }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="currency-amount">{{ number_format($item->quantity, 2) }}</span>
                            @if($item->item && $item->item->unit)
                                <br><small class="text-muted">{{ $item->item->unit }}</small>
                            @endif
                        </td>
                        <td class="text-right">
                            <span class="currency-amount">{{ number_format($item->unit_price, 2) }}</span>
                        </td>
                        <td class="text-right">
                            @if($lineDiscount > 0)
                                <span class="text-danger currency-amount">{{ number_format($lineDiscount, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <strong class="currency-amount text-primary">{{ number_format($lineFinal, 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
                
                <!-- Totals Section -->
                <tr class="table-info">
                    <td colspan="6" class="text-center font-weight-bold">
                        <i class="fas fa-calculator text-primary"></i> الإجماليات
                    </td>
                </tr>
                
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-plus text-success"></i></td>
                    <td colspan="4"><strong>المجموع الفرعي</strong></td>
                    <td class="text-right">
                        <strong class="currency-amount">{{ number_format($subtotal, 2) }}</strong>
                    </td>
                </tr>
                
                @if($totalDiscount > 0)
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-minus text-danger"></i></td>
                    <td colspan="4"><strong>إجمالي الخصم</strong></td>
                    <td class="text-right">
                        <strong class="text-danger currency-amount">{{ number_format($totalDiscount, 2) }}</strong>
                    </td>
                </tr>
                @endif
                
                @if($invoice->tax_amount > 0)
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-percent text-warning"></i></td>
                    <td colspan="4"><strong>الضريبة ({{ $invoice->tax_rate }}%)</strong></td>
                    <td class="text-right">
                        <strong class="text-warning currency-amount">{{ number_format($invoice->tax_amount, 2) }}</strong>
                    </td>
                </tr>
                @endif
                
                <tr class="table-total bg-primary text-white">
                    <td class="text-center"><i class="fas fa-equals"></i></td>
                    <td colspan="4"><strong>المجموع النهائي</strong></td>
                    <td class="text-right">
                        <strong class="currency-amount" style="font-size: 18px;">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Payments Section -->
<div class="payments-section mb-4">
    <h4 class="section-title">
        <i class="fas fa-credit-card text-success"></i> المدفوعات
    </h4>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">رقم السند</th>
                    <th width="25%">التاريخ</th>
                    <th width="25%">المبلغ</th>
                    <th width="25%">العملة</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPaid = 0; @endphp
                @foreach($payments as $i => $payment)
                    @php $paidAmount = $payment->transactions->sum('amount'); @endphp
                    @php $totalPaid += $paidAmount; @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $payment->voucher_number }}</span>
                        </td>
                        <td class="text-center">{{ $payment->date }}</td>
                        <td class="text-right">
                            <span class="text-success currency-amount">{{ number_format($paidAmount, 2) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">{{ $payment->currency }}</span>
                        </td>
                    </tr>
                @endforeach
                
                @if(count($payments) == 0)
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <i class="fas fa-info-circle text-muted"></i>
                        لم يتم تسجيل أي مدفوعات بعد
                    </td>
                </tr>
                @else
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-calculator text-primary"></i></td>
                    <td colspan="3"><strong>إجمالي المدفوع</strong></td>
                    <td class="text-right">
                        <strong class="text-success currency-amount">{{ number_format($totalPaid, 2) }} {{ $invoice->currency }}</strong>
                    </td>
                </tr>
                
                @php $remaining = $invoice->total - $totalPaid; @endphp
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-balance-scale text-warning"></i></td>
                    <td colspan="3"><strong>المتبقي</strong></td>
                    <td class="text-right">
                        <strong class="currency-amount {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($remaining, 2) }} {{ $invoice->currency }}
                        </strong>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Invoice Status -->
<div class="invoice-status mb-4">
    <div class="alert alert-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'partial' ? 'warning' : 'danger') }} text-center">
        <h5 class="mb-0">
            <i class="fas fa-{{ $invoice->status == 'paid' ? 'check-circle' : ($invoice->status == 'partial' ? 'clock' : 'exclamation-circle') }}"></i>
            حالة الفاتورة: 
            @if($invoice->status == 'paid')
                <strong>مدفوعة بالكامل</strong>
            @elseif($invoice->status == 'partial')
                <strong>مدفوعة جزئياً</strong>
            @else
                <strong>غير مدفوعة</strong>
            @endif
        </h5>
    </div>
</div>

<!-- Signature Section -->
<div class="signature-section page-break-inside-avoid">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">توقيع المحاسب</div>
        <div class="signature-name">{{ $invoice->user->name ?? '' }}</div>
    </div>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">توقيع العميل</div>
        <div class="signature-name">{{ $invoice->customer->name ?? '' }}</div>
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

.customer-info {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #3498db;
}

.customer-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.customer-contact {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 3px;
}

.customer-contact i {
    color: #3498db;
    margin-left: 5px;
    width: 12px;
}

.invoice-status .alert {
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

.bg-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
}

@media print {
    .section-title {
        font-size: 16px;
        margin-bottom: 15px;
        padding-bottom: 8px;
    }
    
    .invoice-status .alert {
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
    .items-section, .payments-section {
        page-break-inside: avoid;
    }
    
    .table thead {
        display: table-header-group;
    }
    
    .table tbody {
        display: table-row-group;
    }
    
    .bg-primary {
        background: #3498db !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection 