@extends('layouts.print')

@php
// Handle logo path correctly
if ($printSettings->company_logo) {
    // Print settings logo (already includes print-logos/ path)
    $companyLogo = $printSettings->company_logo;
} else {
    // Fallback to general settings logo (needs logos/ prefix)
    $generalLogo = \App\Models\Setting::get('company_logo');
    $companyLogo = $generalLogo ? 'logos/' . $generalLogo : null;
}
@endphp

@section('print-content')
<style>
/* Compact Professional Invoice */
.compact-invoice {
    font-family: 'Tahoma', Arial, sans-serif;
    direction: rtl;
    text-align: right;
    margin: 0;
    padding: 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.invoice-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: 10px;
}

/* Header Design */
.invoice-header {
    background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #3182ce 100%);
    color: white;
    padding: 20px 30px;
    position: relative;
    overflow: hidden;
}

.invoice-header::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: linear-gradient(45deg, rgba(255,165,0,0.3), rgba(255,140,0,0.2));
    border-radius: 50%;
}

.invoice-header::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.header-content {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.invoice-title-section h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 5px 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.invoice-number {
    font-size: 14px;
    opacity: 0.9;
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 15px;
    display: inline-block;
}

.logo-section {
    text-align: center;
    flex-shrink: 0;
}

.company-logo {
    max-height: 70px;
    max-width: 100px;
    object-fit: contain;
    background: rgba(255,255,255,0.9);
    padding: 8px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Company Details Bar */
.company-bar {
    background: linear-gradient(90deg, #e2e8f0 0%, #cbd5e0 100%);
    padding: 15px 30px;
    border-bottom: 3px solid #3182ce;
}

.company-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.company-info, .invoice-info {
    flex: 1;
    min-width: 200px;
}

.company-info h3, .invoice-info h3 {
    color: #2d3748;
    font-size: 16px;
    font-weight: bold;
    margin: 0 0 8px 0;
    border-bottom: 2px solid #3182ce;
    padding-bottom: 3px;
    display: inline-block;
}

.detail-line {
    color: #4a5568;
    font-size: 13px;
    margin: 3px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.detail-line i {
    color: #3182ce;
    width: 12px;
}

/* Customer Section */
.customer-section {
    background: linear-gradient(45deg, #f7fafc 0%, #edf2f7 100%);
    padding: 15px 30px;
    border-bottom: 2px solid #e2e8f0;
}

.customer-info {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border-right: 4px solid #38a169;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.customer-info h4 {
    color: #38a169;
    font-size: 14px;
    font-weight: bold;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Items Section */
.items-section {
    padding: 0;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.items-table thead th {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    color: white;
    padding: 12px 8px;
    font-size: 13px;
    font-weight: bold;
    text-align: center;
    border: none;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

.items-table tbody {
    height: auto;
}

.items-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
}

.items-table tbody tr:nth-child(even) {
    background: #f7fafc;
}

.items-table tbody tr:hover {
    background: rgba(49, 130, 206, 0.05);
}

.items-table tbody td {
    padding: 10px 8px;
    font-size: 13px;
    text-align: center;
    vertical-align: middle;
}

.item-name {
    font-weight: 600;
    color: #2d3748;
    text-align: right;
}

.item-code {
    color: #718096;
    font-size: 11px;
    margin-top: 2px;
}

.amount-cell {
    background: linear-gradient(45deg, #3182ce, #4299e1);
    color: white;
    font-weight: bold;
    border-radius: 4px;
    padding: 6px 8px;
    margin: 2px;
    display: inline-block;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    font-size: 12px;
}

/* Totals Section */
.totals-section {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 15px 30px;
}

.totals-table {
    width: 100%;
    max-width: 400px;
    margin-right: auto;
    border-collapse: collapse;
}

.totals-table td {
    padding: 8px 15px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.totals-table .label {
    color: #4a5568;
    font-weight: 600;
    text-align: right;
}

.totals-table .amount {
    color: #2d3748;
    font-weight: bold;
    text-align: left;
}

.total-final {
    background: linear-gradient(45deg, #2d3748, #4a5568);
    color: white;
    font-size: 16px;
    font-weight: bold;
}

.total-final td {
    border: none;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

/* Status Badge */
.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.status-unpaid { background: linear-gradient(45deg, #e53e3e, #c53030); color: white; }
.status-paid { background: linear-gradient(45deg, #38a169, #2f855a); color: white; }
.status-partial { background: linear-gradient(45deg, #ed8936, #dd6b20); color: white; }
.status-draft { background: linear-gradient(45deg, #718096, #4a5568); color: white; }

/* Signatures Section */
.signature-box {
    background: white;
    border: 2px dashed #cbd5e0;
    height: 80px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0aec0;
    font-size: 14px;
    margin-top: 10px;
    min-height: 80px;
}

/* Footer Section */
.invoice-footer {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    color: white;
    padding: 15px 30px;
    margin-top: auto;
    min-height: 70px;
    display: flex;
    align-items: center;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    gap: 15px;
    width: 100%;
    font-size: 14px;
    line-height: 1.4;
}

/* Print Optimizations */
@media print {
    .compact-invoice { background: white; }
    .invoice-container { 
        margin: 0; 
        box-shadow: none; 
        border-radius: 0;
    }
    
    .invoice-header::before,
    .invoice-header::after {
        -webkit-print-color-adjust: exact;
    }
    
    .table-header,
    .total-final,
    .amount-cell {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Print Specific Styles */
@media print {
    .compact-invoice {
        min-height: 100vh;
        background: white;
        display: flex;
        flex-direction: column;
    }
    
    .invoice-container {
        box-shadow: none;
        border-radius: 0;
        margin: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .invoice-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        margin: 0;
        z-index: 10;
        padding: 15px 30px;
        min-height: 70px;
        box-sizing: border-box;
    }
    
    .footer-content {
        font-size: 14px;
        line-height: 1.4;
        gap: 20px;
    }
    
    .print-actions {
        display: none !important;
    }
    
    /* Add bottom margin to content to avoid footer overlap */
    .items-section {
        margin-bottom: 150px;
    }
}
</style>

<div class="compact-invoice">
    <div class="invoice-container">
        <!-- Compact Header -->
        <div class="invoice-header">
            <div class="header-content">
                <div class="invoice-title-section">
                    <h1>فــاتــورة مــبــيــعــات</h1>
                    <div class="invoice-number">رقم: {{ $invoice->invoice_number }}</div>
                </div>
                <div class="logo-section">
                    @if($printSettings->show_company_logo && $companyLogo)
                        <img src="{{ asset('storage/' . $companyLogo) }}" 
                             alt="شعار الشركة" 
                             class="company-logo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div style="background: rgba(255,255,255,0.8); padding: 15px; border-radius: 8px; display: none; border: 2px solid #ddd;">
                            <i class="fas fa-building" style="font-size: 24px; color: #666;"></i>
                            <br><small style="color: #666;">الشعار غير متاح</small>
                        </div>
                    @else
                        <div style="background: rgba(255,255,255,0.8); padding: 15px; border-radius: 8px; border: 2px solid #ddd;">
                            <i class="fas fa-building" style="font-size: 24px; color: #666;"></i>
                            <br><small style="color: #666;">الشعار</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Company Details Bar -->
        <div class="company-bar">
                <div class="company-details">
                    <div class="company-info">
                        <h3>{{ $printSettings->company_name ?: 'اسم الشركة' }}</h3>
                        @if($printSettings->company_address)
                            <div class="detail-line"><i class="fas fa-map-marker-alt"></i>{{ $printSettings->company_address }}</div>
                        @endif
                        @if($printSettings->company_phone)
                            <div class="detail-line"><i class="fas fa-phone"></i>{{ $printSettings->company_phone }}</div>
                        @endif
                        @if($printSettings->company_email)
                            <div class="detail-line"><i class="fas fa-envelope"></i>{{ $printSettings->company_email }}</div>
                        @endif
                    </div>
                    
                    <div class="invoice-info">
                        <h3>معلومات الفاتورة</h3>
                        <div class="detail-line">
                            <strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('Y-m-d') }}
                        </div>
                        <div class="detail-line">
                            <strong>العملة:</strong> {{ $invoice->currency }}
                        </div>
                        <div class="detail-line">
                            <strong>الحالة:</strong>
                            <span class="status-badge status-{{ $invoice->status }}">
                                @php
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'unpaid' => 'غير مدفوعة', 
                                        'partial' => 'جزئية',
                                        'paid' => 'مدفوعة'
                                    ];
                                @endphp
                                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Section -->
            <div class="customer-section">
                <div class="customer-info">
                    <h4><i class="fas fa-user"></i>بيانات العميل</h4>
                    <div><strong>{{ $invoice->customer->name ?? 'عميل تجريبي' }}</strong></div>
                    @if(isset($invoice->customer->email))
                        <div class="detail-line"><i class="fas fa-envelope"></i>{{ $invoice->customer->email }}</div>
                    @endif
                    @if(isset($invoice->customer->phone))
                        <div class="detail-line"><i class="fas fa-phone"></i>{{ $invoice->customer->phone }}</div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="items-section">
                <table class="items-table">
                    <thead class="table-header">
                        <tr>
                            <th width="8%">م</th>
                            <th width="40%">الصنف/الخدمة</th>
                            <th width="10%">الكمية</th>
                            <th width="15%">السعر</th>
                            <th width="12%">الخصم</th>
                            <th width="15%">الإجمالي</th>
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
                                <td><strong>{{ $index++ }}</strong></td>
                                <td style="text-align: right;">
                                    <div class="item-name">{{ $item->item->name ?? 'منتج تجريبي ' . ($index-1) }}</div>
                                    @if(isset($item->item->code))
                                        <div class="item-code">كود: {{ $item->item->code }}</div>
                                    @endif
                                </td>
                                <td>{{ number_format($item->quantity, 0) }}</td>
                                <td><span class="amount-cell">{{ number_format($item->unit_price, 0) }}</span></td>
                                <td>
                                    @if($lineDiscount > 0)
                                        <span style="color: #e53e3e; font-weight: bold;">{{ number_format($lineDiscount, 0) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="amount-cell">{{ number_format($lineFinal, 0) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">المجموع الفرعي:</td>
                        <td class="amount">{{ number_format($subtotal + $totalDiscount, 0) }} {{ $invoice->currency }}</td>
                    </tr>
                    @if($totalDiscount > 0)
                    <tr>
                        <td class="label">إجمالي الخصم:</td>
                        <td class="amount" style="color: #e53e3e;">{{ number_format($totalDiscount, 0) }} {{ $invoice->currency }}</td>
                    </tr>
                    @endif
                    <tr class="total-final">
                        <td>المجموع النهائي:</td>
                        <td>{{ number_format($invoice->total, 0) }} {{ $invoice->currency }}</td>
                    </tr>
                </table>
            </div>

            <!-- Signatures -->
            @if($printSettings->show_signature_section)
            <div style="padding: 20px 30px; background: #f7fafc; margin-top: 20px;">
                <h4 style="color: #2d3748; margin: 0 0 15px 0; font-size: 16px; text-align: center;">التوقيعات</h4>
                <div style="display: flex; gap: 30px;">
                    <div style="flex: 1;">
                        <h5 style="color: #2d3748; margin: 0 0 10px 0; font-weight: bold;">توقيع المسؤول</h5>
                        <div class="signature-box">التوقيع والختم</div>
                    </div>
                    <div style="flex: 1;">
                        <h5 style="color: #2d3748; margin: 0 0 10px 0; font-weight: bold;">توقيع العميل</h5>
                        <div class="signature-box">التوقيع والتاريخ</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="invoice-footer">
                <div class="footer-content">
                    <div style="flex: 1; text-align: right;">{{ $printSettings->custom_footer_text ?: 'شكراً لتعاملكم معنا' }}</div>
                    <div style="flex: 1; text-align: center;">تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                    @if($printSettings->show_page_numbers)
                        <div style="flex: 0 0 auto; text-align: left; min-width: 80px;">صفحة 1</div>
                    @endif
                </div>
            </div>
    </div>

    @if($printSettings->enable_watermark && $printSettings->watermark_text)
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); 
                    font-size: 50px; color: rgba(0,0,0,0.05); font-weight: bold; pointer-events: none; z-index: -1;">
            {{ $printSettings->watermark_text }}
        </div>
    @endif
</div>

@if($printSettings->show_invoice_qr_code)
<div style="position: fixed; top: 15px; left: 15px; background: white; padding: 10px; border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 2px solid #3182ce;">
    <div style="width: 60px; height: 60px; border: 2px dashed #cbd5e0; display: flex; align-items: center; 
                justify-content: center; border-radius: 4px; background: #f7fafc;">
        <small style="color: #4a5568;">QR</small>
    </div>
    <small style="display: block; text-align: center; margin-top: 5px; color: #4a5568;">للتحقق</small>
</div>
@endif
@endsection 