@extends('layouts.app')

@section('content')
@php
    use App\Models\Setting;
    $companyLogo = Setting::get('company_logo');
    $companyName = Setting::get('company_name', 'نظام المحاسبة المتكامل');
    $companyAddress = Setting::get('company_address', '');
    $companyPhone = Setting::get('company_phone', '');
    $companyEmail = Setting::get('company_email', '');
    $rtl = app()->getLocale() == 'ar';
@endphp

<div class="print-wrapper">
    <!-- Print Actions - Hidden in print -->
    <div class="no-print print-actions text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> طباعة
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> إغلاق
        </button>
    </div>

    <!-- Print Container -->
    <div class="professional-print-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-info-section">
                @if ($companyLogo)
                <div class="company-logo-wrapper">
                    <img src="{{ asset('storage/logos/' . $companyLogo) }}" alt="{{ $companyName }}" class="company-logo">
                </div>
                @endif
                <div class="company-details">
                    <h1 class="company-name">{{ $companyName }}</h1>
                    @if($companyAddress)
                        <div class="company-address"><i class="fas fa-map-marker-alt"></i> {{ $companyAddress }}</div>
                    @endif
                    <div class="company-contact">
                        @if($companyPhone)
                            <span><i class="fas fa-phone"></i> {{ $companyPhone }}</span>
                        @endif
                        @if($companyEmail)
                            <span><i class="fas fa-envelope"></i> {{ $companyEmail }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="print-metadata">
                <div class="print-date">تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                <div class="print-by">طُبع بواسطة: {{ auth()->user()->name ?? 'النظام' }}</div>
            </div>
        </div>

        <!-- Separator Line -->
        <div class="header-separator"></div>
        
        <!-- Document Content -->
        <div class="document-content">
            @yield('print-content')
        </div>
        
        <!-- Footer -->
        <div class="document-footer">
            <div class="footer-separator"></div>
            <div class="footer-content">
                <div class="footer-left">
                    <small>تم إنشاؤه بواسطة {{ $companyName }}</small>
                </div>
                <div class="footer-center">
                    <small>صفحة <span class="page-number"></span></small>
                </div>
                <div class="footer-right">
                    <small>جميع الحقوق محفوظة &copy; {{ date('Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ==============================================
   PROFESSIONAL PRINT SYSTEM STYLES
   ============================================== */

/* Print Media Queries */
@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    @page {
        size: A4;
        margin: 1cm 1.5cm;
        margin-top: 1cm;
        margin-bottom: 1.5cm;
    }
    
    html, body {
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Cairo', 'Tajawal', Arial, sans-serif !important;
        font-size: 14px !important;
        line-height: 1.4 !important;
        color: #000 !important;
        background: #fff !important;
    }
    
    /* Hide unnecessary elements */
    .navbar, .nav-link, .main-sidebar, .main-header, .main-footer,
    .content-header, .no-print, .print-actions,
    h1:first-child, .page-header, .app-header, #app > header,
    .btn, .alert, .breadcrumb {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        width: 100% !important;
        min-height: auto !important;
    }
    
    .print-wrapper {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}

/* General Styles */
.print-wrapper {
    direction: rtl;
    font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
    background: #fff;
    color: #000;
    min-height: 100vh;
}

.professional-print-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 8px;
}

/* ==============================================
   COMPANY HEADER STYLES
   ============================================== */
.company-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
    padding: 20px 0;
}

.company-info-section {
    display: flex;
    align-items: center;
    gap: 20px;
}

.company-logo-wrapper {
    flex-shrink: 0;
}

.company-logo {
    max-height: 100px;
    max-width: 150px;
    object-fit: contain;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px;
    background: #fff;
}

.company-details {
    flex-grow: 1;
}

.company-name {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.company-address {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 8px;
}

.company-contact {
    color: #6c757d;
    font-size: 13px;
}

.company-contact span {
    margin-left: 15px;
    display: inline-block;
}

.company-contact i {
    color: #3498db;
    margin-left: 5px;
    width: 12px;
}

.print-metadata {
    text-align: left;
    color: #6c757d;
    font-size: 12px;
    flex-shrink: 0;
}

.print-date, .print-by {
    margin-bottom: 5px;
}

/* ==============================================
   SEPARATOR STYLES
   ============================================== */
.header-separator {
    height: 4px;
    background: linear-gradient(90deg, #3498db 0%, #2ecc71 50%, #3498db 100%);
    border-radius: 2px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
}

.footer-separator {
    height: 2px;
    background: linear-gradient(90deg, #95a5a6 0%, #bdc3c7 50%, #95a5a6 100%);
    border-radius: 1px;
    margin-bottom: 15px;
}

/* ==============================================
   DOCUMENT CONTENT STYLES
   ============================================== */
.document-content {
    min-height: 400px;
    margin-bottom: 40px;
}

/* Document Title */
.document-title {
    text-align: center;
    margin-bottom: 30px;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    border-right: 4px solid #3498db;
}

.document-title h3 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* Document Info Section */
.document-info {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.document-info .row {
    margin-bottom: 15px;
}

.document-info .row:last-child {
    margin-bottom: 0;
}

.document-info strong {
    color: #2c3e50;
    font-weight: 600;
}

/* Badge Styles */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    color: #fff;
    box-shadow: 0 2px 4px rgba(39, 174, 96, 0.3);
}

.badge-danger {
    background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
    color: #fff;
    box-shadow: 0 2px 4px rgba(192, 57, 43, 0.3);
}

.badge-info {
    background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
    color: #fff;
    box-shadow: 0 2px 4px rgba(41, 128, 185, 0.3);
}

/* ==============================================
   TABLE STYLES
   ============================================== */
.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background: #fff;
    margin-bottom: 0;
}

.table th {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    color: #fff;
    font-weight: 600;
    text-align: center;
    padding: 15px 12px;
    border: 1px solid #2c3e50;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: 12px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
    background: #fff;
}

.table tbody tr:nth-child(even) {
    background: #f8f9fa;
}

.table tbody tr:hover {
    background: #e3f2fd;
}

/* Table Total Rows */
.table-total {
    background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%) !important;
    font-weight: 700;
    color: #2c3e50;
}

.table-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    font-weight: 600;
    color: #1976d2;
}

/* Text Alignment */
.text-right { text-align: right; }
.text-center { text-align: center; }
.text-left { text-align: left; }

/* Number Formatting */
.currency-amount {
    font-weight: 600;
    color: #2c3e50;
    font-family: 'Courier New', monospace;
}

.text-debit {
    color: #27ae60;
    font-weight: 600;
}

.text-credit {
    color: #e74c3c;
    font-weight: 600;
}

/* ==============================================
   SIGNATURE SECTION
   ============================================== */
.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 50px;
    margin-bottom: 30px;
    gap: 30px;
}

.signature-box {
    text-align: center;
    min-width: 200px;
    flex: 1;
}

.signature-line {
    display: block;
    border-top: 2px solid #2c3e50;
    width: 100%;
    margin: 40px 0 10px 0;
}

.signature-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ==============================================
   FOOTER STYLES
   ============================================== */
.document-footer {
    margin-top: 40px;
    page-break-inside: avoid;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #6c757d;
    padding: 10px 0;
}

.footer-left, .footer-center, .footer-right {
    flex: 1;
    text-align: center;
}

.footer-left {
    text-align: right;
}

.footer-right {
    text-align: left;
}

/* ==============================================
   PRINT ACTIONS
   ============================================== */
.print-actions {
    margin-bottom: 30px;
    text-align: center;
}

.print-actions .btn {
    margin: 0 10px;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.print-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    color: #fff;
}

.btn-secondary {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    border: none;
    color: #fff;
}

/* ==============================================
   RESPONSIVE PRINT DESIGN
   ============================================== */
@media print {
    .professional-print-container {
        box-shadow: none;
        border-radius: 0;
        padding: 0;
        max-width: none;
    }
    
    .company-header {
        margin-bottom: 20px;
        padding: 10px 0;
    }
    
    .company-name {
        font-size: 24px;
    }
    
    .document-title h3 {
        font-size: 20px;
    }
    
    .table th {
        padding: 10px 8px;
        font-size: 12px;
    }
    
    .table td {
        padding: 8px;
        font-size: 12px;
    }
    
    .signature-section {
        margin-top: 30px;
        margin-bottom: 20px;
    }
    
    .signature-line {
        margin: 30px 0 8px 0;
    }
    
    /* Page break controls */
    .page-break-before {
        page-break-before: always;
    }
    
    .page-break-after {
        page-break-after: always;
    }
    
    .page-break-inside-avoid {
        page-break-inside: avoid;
    }
}

/* ==============================================
   ANIMATIONS & EFFECTS
   ============================================== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.document-content > * {
    animation: fadeIn 0.5s ease-out;
}

/* ==============================================
   RTL SUPPORT
   ============================================== */
[dir="rtl"] .print-wrapper {
    direction: rtl;
}

[dir="rtl"] .company-contact span {
    margin-right: 15px;
    margin-left: 0;
}

[dir="rtl"] .company-contact i {
    margin-right: 5px;
    margin-left: 0;
}

[dir="rtl"] .footer-left {
    text-align: left;
}

[dir="rtl"] .footer-right {
    text-align: right;
}
</style>
@endpush 