@extends('layouts.app')

@section('content')
@php
    use App\Models\Setting;
    $companyLogo = Setting::get('company_logo');
    $companyName = Setting::get('company_name', '');
    $rtl = app()->getLocale() == 'ar';
@endphp
<div class="container print-container">
    <div class="print-header text-center mb-4">
        @if ($companyLogo)
        <div class="logo-container mb-2">
            <img src="{{ asset('storage/logos/' . $companyLogo) }}" alt="{{ $companyName }}" class="company-logo">
        </div>
        @endif
        <h2 class="company-name">{{ $companyName }}</h2>
        <div class="print-date">{{ __('messages.print_date') }}: {{ now()->format('Y-m-d H:i') }}</div>
        <hr class="divider">
    </div>
    
    <div class="print-body">
        @yield('print-content')
    </div>
    
    <div class="print-footer text-center mt-5">
        <hr class="divider">
        <div class="footer-content">
            <small>{{ __('messages.generated_by_system') }} - {{ __('messages.all_rights_reserved') }} &copy; {{ date('Y') }}</small>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* General Print Styles */
@media print {
    body {
        font-family: {{ $rtl ? "'Tajawal'" : "'Poppins'" }}, sans-serif !important;
        background: #fff !important;
        color: #000 !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 12pt !important;
    }
    
    .print-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px !important;
        box-sizing: border-box !important;
    }
    
    .navbar, .nav-link, .main-sidebar, .main-header, .main-footer,
    .btn-primary, .mode-toggle, .no-print, .content-header,
    h1:first-of-type, .page-header, .app-header, #app > header {
        display: none !important;
    }

    /* Ocultar encabezados de página al imprimir */
    @page {
        margin-top: 0;
        margin-header: 0;
        margin-footer: 0;
    }
    
    /* Eliminar encabezados generados por el navegador */
    html, body { 
        height: auto; 
    }
    
    /* Ocultar elementos específicos en la parte superior */
    body::before, 
    #app::before, 
    .content-wrapper::before,
    .app-title,
    .page-title {
        display: none !important;
        height: 0 !important;
        visibility: hidden !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        width: 100% !important;
        min-height: auto !important; 
    }
    
    .print-header, .print-footer { page-break-inside: avoid; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .table { border-collapse: collapse !important; }
    .table th, .table td { background: #fff !important; }
}

/* Print Layout Styles */
.print-container {
    direction: {{ $rtl ? 'rtl' : 'ltr' }};
    font-family: {{ $rtl ? "'Tajawal'" : "'Poppins'" }}, sans-serif;
    background: #fff;
    color: #000;
    padding: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.company-logo {
    max-height: 80px;
    max-width: 200px;
    object-fit: contain;
}

.company-name {
    margin: 10px 0;
    font-weight: 700;
    color: #2c3e50;
    font-size: 24px;
}

.print-date {
    color: #6c757d;
    font-size: 14px;
    font-style: italic;
}

.divider {
    border-top: 2px solid #3498db;
    margin: 15px 0;
}

.footer-content {
    color: #6c757d;
    font-size: 12px;
}

/* Table Styling */
.print-container table {
    width: 100%;
    margin-bottom: 1rem;
    border-collapse: collapse;
}

.print-container th {
    background-color: #f8f9fa;
    color: #2c3e50;
    font-weight: bold;
    text-align: {{ $rtl ? 'right' : 'left' }};
    padding: 0.75rem;
    vertical-align: top;
    border: 1px solid #dee2e6;
}

.print-container td {
    padding: 0.75rem;
    vertical-align: top;
    border: 1px solid #dee2e6;
}

.print-container .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.03);
}

/* Print buttons */
.print-actions {
    margin-bottom: 20px;
}

.print-actions .btn {
    margin-right: 10px;
    margin-bottom: 10px;
}

.print-actions .btn i {
    margin-right: 5px;
}

/* Signature section */
.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 50px;
    margin-bottom: 30px;
}

.signature-box {
    text-align: center;
    min-width: 200px;
}

.signature-line {
    display: inline-block;
    border-top: 1px solid #000;
    width: 150px;
    margin-bottom: 5px;
}

.signature-title {
    font-size: 14px;
    color: #666;
}

/* Specific document styling */
.document-title {
    color: #3498db;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    font-size: 20px;
}

.document-info {
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.table-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #3498db;
}

.table-total {
    font-weight: bold;
    background-color: #f8f9fa;
}
</style>
@endpush 