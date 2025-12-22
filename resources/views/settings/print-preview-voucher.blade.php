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

@push('styles')
<style>
/* Professional Voucher Design - Same as Invoice */
.compact-voucher {
    font-family: 'Tahoma', Arial, sans-serif;
    direction: rtl;
    text-align: right;
    margin: 0;
    padding: 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.voucher-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: 10px;
}

/* Header Design - Compact */
.voucher-header {
    background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #3182ce 100%);
    color: white;
    padding: 12px 20px;
    position: relative;
    overflow: hidden;
}

.voucher-header::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.voucher-header::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: -20px;
    width: 80px;
    height: 80px;
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

.voucher-title-section h1 {
    font-size: 20px;
    font-weight: bold;
    margin: 0 0 3px 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.voucher-number {
    font-size: 12px;
    background: rgba(255,255,255,0.2);
    padding: 3px 6px;
    border-radius: 4px;
    display: inline-block;
}

.logo-section {
    text-align: center;
    flex-shrink: 0;
}

.company-logo {
    max-height: 50px;
    max-width: 80px;
    object-fit: contain;
    background: rgba(255,255,255,0.9);
    padding: 5px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Company Details Bar - Compact */
.company-bar {
    background: linear-gradient(90deg, #e2e8f0 0%, #cbd5e0 100%);
    padding: 10px 20px;
    border-bottom: 2px solid #3182ce;
}

.company-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.company-info h3 {
    color: #2d3748;
    font-size: 14px;
    font-weight: bold;
    margin: 0 0 5px 0;
}

.detail-line {
    color: #4a5568;
    font-size: 11px;
    margin: 2px 0;
    display: flex;
    align-items: center;
    gap: 4px;
}

.detail-line i {
    color: #3182ce;
    width: 10px;
    font-size: 10px;
}

.voucher-info h3 {
    color: #2d3748;
    font-size: 14px;
    font-weight: bold;
    margin: 0 0 5px 0;
}

/* Customer Section - Compact */
.customer-section {
    background: linear-gradient(45deg, #f7fafc 0%, #edf2f7 100%);
    padding: 8px 20px;
    border-bottom: 1px solid #e2e8f0;
}

.customer-info {
    background: white;
    padding: 8px 12px;
    border-radius: 6px;
    border-right: 3px solid #38a169;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
}

.customer-info h4 {
    color: #38a169;
    font-size: 12px;
    font-weight: bold;
    margin: 0 0 5px 0;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Transaction Table - Compact */
.transactions-section {
    padding: 0;
}

.transactions-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.transactions-table thead th {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    color: white;
    padding: 8px 6px;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    border: none;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

.transactions-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
}

.transactions-table tbody tr:nth-child(even) {
    background: #f7fafc;
}

.transactions-table tbody tr:hover {
    background: rgba(49, 130, 206, 0.05);
}

.transactions-table tbody td {
    padding: 6px 5px;
    font-size: 11px;
    text-align: center;
    vertical-align: middle;
}

.account-name {
    font-weight: 600;
    color: #2d3748;
    text-align: right;
    font-size: 10px;
    padding: 4px 2px;
}

.amount-cell {
    font-weight: bold;
    color: #38a169;
    font-size: 11px;
}

.debit-amount {
    color: #e53e3e;
}

.credit-amount {
    color: #38a169;
}

/* Total Section - Compact */
.total-section {
    background: #f7fafc;
    padding: 8px 20px;
    border-top: 1px solid #e2e8f0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 6px 15px;
    border-radius: 6px;
    margin: 3px 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    font-size: 12px;
}

.total-final {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    color: white;
    font-weight: bold;
    font-size: 14px;
}

/* Notes Section - Compact */
.notes-section {
    background: #f7fafc;
    padding: 8px 20px;
    margin: 5px 0;
}

.notes-box {
    background: white;
    border: 2px dashed #cbd5e0;
    border-radius: 6px;
    padding: 8px;
    min-height: 40px;
    color: #4a5568;
    font-size: 11px;
}

/* Signatures Section - Compact */
.signature-box {
    background: white;
    border: 2px dashed #cbd5e0;
    height: 50px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0aec0;
    font-size: 11px;
    margin-top: 6px;
    min-height: 50px;
}

/* Footer Section - Compact */
.voucher-footer {
    background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    color: white;
    padding: 8px 20px;
    margin-top: auto;
    min-height: 40px;
    display: flex;
    align-items: center;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap;
    gap: 10px;
    width: 100%;
    font-size: 11px;
    line-height: 1.2;
}

/* Print Specific Styles */
@media print {
    .compact-voucher {
        min-height: 100vh;
        background: white;
        display: flex;
        flex-direction: column;
    }
    
    .voucher-container {
        box-shadow: none;
        border-radius: 0;
        margin: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .voucher-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        margin: 0;
        z-index: 10;
        padding: 5px 20px;
        min-height: 30px;
        box-sizing: border-box;
    }
    
    .footer-content {
        font-size: 10px;
        line-height: 1.1;
        gap: 8px;
    }
    
    .print-actions {
        display: none !important;
    }
    
    /* Reduce spacing for print */
    .voucher-header {
        padding: 8px 15px;
    }
    
    .company-bar {
        padding: 6px 15px;
    }
    
    .customer-section {
        padding: 5px 15px;
    }
    
    .total-section {
        padding: 5px 15px;
    }
    
    .notes-section {
        padding: 5px 15px;
        margin: 3px 0;
    }
    
    /* Add minimal bottom margin to content */
    .transactions-section {
        margin-bottom: 60px;
    }
    
    /* Compact signatures for print */
    .signature-box {
        height: 35px;
        min-height: 35px;
        font-size: 10px;
    }
}

/* Status Badges */
.status-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
}

.status-draft { background: #fed7d7; color: #c53030; }
.status-approved { background: #c6f6d5; color: #2f855a; }
.status-posted { background: #bee3f8; color: #2b6cb0; }
.status-cancelled { background: #fed7e2; color: #b83280; }
</style>
@endpush

@section('print-content')
<div class="compact-voucher">
    <div class="voucher-container">
        <!-- Professional Header -->
        <div class="voucher-header">
            <div class="header-content">
                <div class="voucher-title-section">
                    <h1>
                        @if($voucher->type == 'receipt')
                            سـنـد قـبـض
                        @elseif($voucher->type == 'payment') 
                            سـنـد دفـع
                        @else
                            سـنـد تـحـويـل
                        @endif
                    </h1>
                    <div class="voucher-number">رقم: {{ $voucher->voucher_number }}</div>
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
                
                <div class="voucher-info">
                    <h3>معلومات السند</h3>
                    <div class="detail-line">
                        <strong>التاريخ:</strong> {{ $voucher->date ? \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d') : '-' }}
                    </div>
                    <div class="detail-line">
                        <strong>العملة:</strong> {{ $voucher->currency }}
                    </div>
                    <div class="detail-line">
                        <strong>الحالة:</strong>
                        <span class="status-badge status-{{ $voucher->status ?? 'draft' }}">
                            @php
                                $statusLabels = [
                                    'draft' => 'مسودة',
                                    'approved' => 'معتمد',
                                    'posted' => 'مترحل',
                                    'cancelled' => 'ملغي'
                                ];
                            @endphp
                            {{ $statusLabels[$voucher->status ?? 'draft'] ?? 'مسودة' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipient/Payer Information -->
        @if($voucher->recipient_name)
        <div class="customer-section">
            <div class="customer-info">
                <h4>
                    <i class="fas fa-user"></i>
                    @if($voucher->type == 'receipt')
                        الدافع
                    @elseif($voucher->type == 'payment')
                        المستفيد
                    @else
                        الطرف الثاني
                    @endif
                </h4>
                <div class="detail-line">
                    <strong>الاسم:</strong> {{ $voucher->recipient_name }}
                </div>
                @if($voucher->description)
                    <div class="detail-line">
                        <strong>البيان:</strong> {{ $voucher->description }}
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Journal Entries Table -->
        <div class="transactions-section">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th width="4%">#</th>
                        <th width="10%">رقم الحساب</th>
                        <th width="25%">اسم الحساب</th>
                        <th width="12%">مدين</th>
                        <th width="12%">دائن</th>
                        <th width="8%">العملة</th>
                        <th width="9%">سعر الصرف</th>
                        <th width="20%">البيان</th>
                    </tr>
                </thead>
                <tbody>
                    @if($voucher->journalEntry && $voucher->journalEntry->lines)
                        @foreach($voucher->journalEntry->lines as $index => $line)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $line->account->code ?? '-' }}</td>
                            <td class="account-name">{{ $line->account->name ?? 'حساب تجريبي' }}</td>
                            <td class="amount-cell debit-amount">
                                @if($line->debit > 0)
                                    {{ number_format($line->debit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="amount-cell credit-amount">
                                @if($line->credit > 0)
                                    {{ number_format($line->credit, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $line->currency ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                @if($line->exchange_rate && $line->exchange_rate != 1.0)
                                    <strong class="text-info">{{ number_format($line->exchange_rate, 4) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $line->description ?? 'بيان السند' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <!-- Mock data for preview -->
                        <tr>
                            <td>1</td>
                            <td>1001</td>
                            <td class="account-name">الصندوق</td>
                            <td class="amount-cell debit-amount">1,500.00</td>
                            <td class="amount-cell">-</td>
                            <td>استلام نقدية</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>4001</td>
                            <td class="account-name">المبيعات</td>
                            <td class="amount-cell">-</td>
                            <td class="amount-cell credit-amount">1,500.00</td>
                            <td>مبيعات نقدية</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            @php
                // حساب مبلغ السند بناءً على القيود الفعلية
                $voucherAmount = 0;
                $voucherAmountCurrency = $voucher->currency ?? 'MIX';
                
                if ($voucher->journalEntry && $voucher->journalEntry->lines) {
                    // للسندات متعددة العملات: حساب المبلغ الأصلي من المعاملة الأولى
                    // نأخذ المبلغ الأكبر (المدين أو الدائن) من أول سطر
                    $firstLine = $voucher->journalEntry->lines->first();
                    if ($firstLine) {
                        $voucherAmount = max($firstLine->debit, $firstLine->credit);
                        $voucherAmountCurrency = $firstLine->currency;
                    }
                }
            @endphp
            
            @if($voucher->type == 'transfer')
                <!-- للسندات متعددة العملات: عرض المجاميع لكل عملة -->
                @foreach($voucher->journalEntry->lines->groupBy('currency') as $currency => $lines)
                    @php
                        $currDebit = $lines->sum('debit');
                        $currCredit = $lines->sum('credit');
                    @endphp
                    <div class="total-row">
                        <span>إجمالي المدين ({{ $currency }}):</span>
                        <span class="debit-amount">{{ number_format($currDebit, 2) }} {{ $currency }}</span>
                    </div>
                    <div class="total-row">
                        <span>إجمالي الدائن ({{ $currency }}):</span>
                        <span class="credit-amount">{{ number_format($currCredit, 2) }} {{ $currency }}</span>
                    </div>
                @endforeach
            @else
                <!-- للسندات العادية: عرض المجاميع الإجمالية -->
                <div class="total-row">
                    <span>إجمالي المدين:</span>
                    <span class="debit-amount">{{ number_format($voucher->journalEntry->total_debit ?? 0, 2) }} {{ $voucherAmountCurrency }}</span>
                </div>
                <div class="total-row">
                    <span>إجمالي الدائن:</span>
                    <span class="credit-amount">{{ number_format($voucher->journalEntry->total_credit ?? 0, 2) }} {{ $voucherAmountCurrency }}</span>
                </div>
            @endif
            
            <div class="total-row total-final">
                <span>مبلغ السند:</span>
                <span>{{ number_format($voucherAmount, 2) }} {{ $voucherAmountCurrency }}</span>
            </div>
        </div>

        <!-- Notes Section -->
        @if($printSettings->show_notes_section && $voucher->notes)
        <div class="notes-section">
            <h4 style="color: #2d3748; margin: 0 0 5px 0; font-size: 12px;">ملاحظات</h4>
            <div class="notes-box">
                {{ $voucher->notes }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        @if($printSettings->show_signature_section)
        <div style="padding: 10px 20px; background: #f7fafc; margin-top: 5px;">
            <h4 style="color: #2d3748; margin: 0 0 8px 0; font-size: 12px; text-align: center;">التوقيعات</h4>
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <h5 style="color: #2d3748; margin: 0 0 5px 0; font-weight: bold; font-size: 11px;">توقيع المحاسب</h5>
                    <div class="signature-box">التوقيع والختم</div>
                </div>
                <div style="flex: 1;">
                    <h5 style="color: #2d3748; margin: 0 0 5px 0; font-weight: bold; font-size: 11px;">
                        @if($voucher->type == 'receipt')
                            توقيع الدافع
                        @elseif($voucher->type == 'payment')
                            توقيع المستفيد
                        @else
                            توقيع المسؤول
                        @endif
                    </h5>
                    <div class="signature-box">التوقيع والتاريخ</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="voucher-footer">
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

    @if($printSettings->show_invoice_qr_code)
    <div style="position: fixed; top: 15px; left: 15px; background: white; padding: 10px; border-radius: 8px; 
                box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 2px solid #3182ce;">
        <div style="width: 60px; height: 60px; border: 2px dashed #cbd5e0; display: flex; align-items: center; 
                    justify-content: center; border-radius: 4px; background: #f7fafc;">
            <small style="color: #4a5568;">QR</small>
        </div>
    </div>
    @endif
    
</div> <!-- End compact-voucher -->
@endsection

