@extends('layouts.print')

@php
// Handle logo path correctly
if ($printSettings->company_logo) {
    $companyLogo = $printSettings->company_logo;
} else {
    $generalLogo = \App\Models\Setting::get('company_logo');
    $companyLogo = $generalLogo ? 'logos/' . $generalLogo : null;
}
@endphp

@push('styles')
<style>
/* Professional Journal Entry Print Design */
.compact-journal {
    font-family: 'Tahoma', Arial, sans-serif;
    direction: rtl;
    text-align: right;
    margin: 0;
    padding: 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.journal-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: 10px;
}

/* Header Design */
.journal-header {
    background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #3182ce 100%);
    color: white;
    padding: 15px 25px;
    position: relative;
    overflow: hidden;
}

.journal-header::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.journal-header::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: -20px;
    width: 100px;
    height: 100px;
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

.journal-title-section h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0 0 5px 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.journal-number {
    font-size: 14px;
    background: rgba(255,255,255,0.2);
    padding: 4px 10px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 5px;
}

.logo-section {
    text-align: center;
    flex-shrink: 0;
}

.company-logo {
    max-height: 60px;
    max-width: 100px;
    object-fit: contain;
    background: rgba(255,255,255,0.9);
    padding: 5px;
    border-radius: 4px;
}

/* Info Section */
.journal-info {
    padding: 20px 25px;
    background: #f8f9fa;
    border-bottom: 2px solid #e2e8f0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 3px;
    font-weight: 600;
    text-transform: uppercase;
}

.info-value {
    font-size: 14px;
    color: #1e293b;
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 3px;
}

.status-active {
    background: #10b981;
    color: white;
}

.status-cancelled {
    background: #ef4444;
    color: white;
}

/* Description Section */
.description-section {
    padding: 15px 25px;
    background: white;
    border-bottom: 2px solid #e2e8f0;
}

.description-label {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 5px;
    font-weight: 600;
}

.description-text {
    font-size: 14px;
    color: #1e293b;
    line-height: 1.6;
}

/* Table Section */
.lines-section {
    padding: 20px 25px;
    background: white;
}

.section-title {
    font-size: 16px;
    font-weight: bold;
    color: #1e293b;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #3182ce;
}

.journal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin-top: 10px;
}

.journal-table thead {
    background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
    color: white;
}

.journal-table th {
    padding: 12px 8px;
    text-align: right;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    border: 1px solid rgba(255,255,255,0.2);
}

.journal-table td {
    padding: 10px 8px;
    border: 1px solid #e2e8f0;
    text-align: right;
}

.journal-table tbody tr:nth-child(even) {
    background: #f8f9fa;
}

.journal-table tbody tr:hover {
    background: #e0f2fe;
}

.account-name {
    font-weight: 600;
    color: #1e293b;
}

.account-code {
    font-size: 10px;
    color: #64748b;
    margin-right: 5px;
}

.amount-cell {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    text-align: left;
    direction: ltr;
}

.debit-amount {
    color: #059669;
}

.credit-amount {
    color: #dc2626;
}

.currency-badge {
    display: inline-block;
    padding: 2px 6px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
}

.exchange-rate {
    color: #0369a1;
    font-weight: 600;
}

/* Total Row */
.total-row {
    background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
    color: white;
    font-weight: bold;
}

.total-row td {
    border: 1px solid rgba(255,255,255,0.2);
    padding: 12px 8px;
    font-size: 13px;
}

.total-label {
    font-size: 14px;
    text-transform: uppercase;
}

/* Signature Section */
.signature-section {
    padding: 25px;
    background: white;
    border-top: 2px solid #e2e8f0;
    margin-top: 20px;
}

.signature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

.signature-box {
    text-align: center;
}

.signature-line {
    border-top: 2px solid #1e293b;
    width: 100%;
    height: 60px;
    margin-bottom: 5px;
}

.signature-title {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    margin-top: 5px;
}

/* Footer */
.journal-footer {
    padding: 15px 25px;
    background: #f8f9fa;
    border-top: 2px solid #e2e8f0;
    text-align: center;
    font-size: 11px;
    color: #64748b;
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .compact-journal {
        background: white;
        margin: 0;
        padding: 0;
    }
    
    .journal-container {
        box-shadow: none;
        border-radius: 0;
        margin: 0;
    }
    
    .journal-table {
        page-break-inside: avoid;
    }
    
    .journal-table tbody tr {
        page-break-inside: avoid;
    }
    
    .signature-section {
        page-break-inside: avoid;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .journal-table {
        font-size: 10px;
    }
    
    .journal-table th,
    .journal-table td {
        padding: 6px 4px;
    }
}
</style>
@endpush

@section('print-content')
<div class="no-print print-actions text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> @lang('messages.print')
    </button>
    <button onclick="window.close()" class="btn btn-secondary">
        <i class="fas fa-times"></i> @lang('messages.close')
    </button>
</div>

<div class="compact-journal">
    <div class="journal-container">
        <!-- Header -->
        <div class="journal-header">
            <div class="header-content">
                <div class="journal-title-section">
                    <h1>@lang('messages.journal_entry')</h1>
                    <div class="journal-number">
                        رقم القيد: #{{ str_pad($journalEntry->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                @if($companyLogo)
                <div class="logo-section">
                    <img src="{{ asset('storage/' . $companyLogo) }}" alt="Logo" class="company-logo" onerror="this.style.display='none'">
                </div>
                @endif
            </div>
        </div>

        <!-- Info Section -->
        <div class="journal-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">@lang('messages.date')</span>
                    <span class="info-value">
                        @if($journalEntry->date instanceof \Carbon\Carbon)
                            {{ $journalEntry->date->format('Y-m-d') }}
                        @else
                            {{ \Carbon\Carbon::parse($journalEntry->date)->format('Y-m-d') }}
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('messages.status')</span>
                    <span class="status-badge {{ $journalEntry->status == 'active' ? 'status-active' : 'status-cancelled' }}">
                        @if($journalEntry->status == 'active')
                            @lang('messages.status_active')
                        @else
                            @lang('messages.status_cancelled')
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('messages.user')</span>
                    <span class="info-value">{{ $journalEntry->user->name ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('messages.currency')</span>
                    <span class="info-value">
                        <span class="currency-badge">{{ $journalEntry->currency }}</span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('messages.total_debit')</span>
                    <span class="info-value debit-amount">{{ number_format($journalEntry->total_debit, 2) }} {{ $journalEntry->currency }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">@lang('messages.total_credit')</span>
                    <span class="info-value credit-amount">{{ number_format($journalEntry->total_credit, 2) }} {{ $journalEntry->currency }}</span>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        @if($journalEntry->description)
        <div class="description-section">
            <div class="description-label">@lang('messages.description')</div>
            <div class="description-text">{{ $journalEntry->description }}</div>
        </div>
        @endif

        <!-- Lines Section -->
        <div class="lines-section">
            <h4 class="section-title">
                <i class="fas fa-list-alt"></i> @lang('messages.journal_entry_lines')
            </h4>
            <table class="journal-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">@lang('messages.account')</th>
                        <th width="25%">@lang('messages.description')</th>
                        <th width="12%">@lang('messages.debit')</th>
                        <th width="12%">@lang('messages.credit')</th>
                        <th width="8%">@lang('messages.currency')</th>
                        <th width="10%">@lang('messages.exchange_rate')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $i => $line)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="account-name">
                                @if($line->account)
                                    <span class="account-code">({{ $line->account->code ?? '-' }})</span>
                                    {{ $line->account->name }}
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td>{{ $line->description ?? '-' }}</td>
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
                        <td>
                            <span class="currency-badge">{{ $line->currency }}</span>
                        </td>
                        <td class="exchange-rate">
                            @if($line->exchange_rate && $line->exchange_rate != 1.0)
                                {{ number_format($line->exchange_rate, 4) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <!-- Total Row -->
                    <tr class="total-row">
                        <td colspan="3" class="total-label">@lang('messages.total')</td>
                        <td class="amount-cell">
                            {{ number_format($journalEntry->total_debit, 2) }}
                        </td>
                        <td class="amount-cell">
                            {{ number_format($journalEntry->total_credit, 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">@lang('messages.accountant_signature')</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-title">@lang('messages.finance_manager_signature')</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="journal-footer">
            <div>صفحة 1</div>
            <div>تاريخ الطباعة: {{ now()->format('H:i Y-m-d') }}</div>
            <div>شكراً لتعاملكم معنا</div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // Auto-print after a short delay
        setTimeout(function() { 
            window.print(); 
        }, 500);
    };
</script>
@endsection
