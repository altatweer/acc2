<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>معاينة الطباعة - {{ config('app.name', 'نظام المحاسبة') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Tahoma', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        .print-container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .no-print {
            display: block;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                padding: 0;
                max-width: 100%;
                box-shadow: none;
            }
        }
        
        /* Print Button Styling */
        .print-actions {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .print-btn {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,123,255,0.4);
        }
        
        .close-btn {
            background: linear-gradient(45deg, #6c757d, #495057);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(108,117,125,0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108,117,125,0.4);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @php
        use App\Models\Setting;
        use App\Models\PrintSetting;
        
        // Get custom print settings
        $printSettings = PrintSetting::current();
        
        // Handle logo path correctly
        if ($printSettings->company_logo) {
            // Print settings logo (already includes print-logos/ path)
            $companyLogo = $printSettings->company_logo;
        } else {
            // Fallback to general settings logo (needs logos/ prefix)
            $generalLogo = Setting::get('company_logo');
            $companyLogo = $generalLogo ? 'logos/' . $generalLogo : null;
        }
        
        // Other settings
        $companyName = $printSettings->company_name ?: Setting::get('company_name', 'نظام المحاسبة المتكامل');
        $companyAddress = $printSettings->company_address ?: Setting::get('company_address', '');
        $companyPhone = $printSettings->company_phone ?: Setting::get('company_phone', '');
        $companyEmail = $printSettings->company_email ?: Setting::get('company_email', '');
        $companyWebsite = $printSettings->company_website ?: '';
        $rtl = app()->getLocale() == 'ar';
    @endphp

    <!-- Print Actions - Clean and Professional -->
    <div class="print-actions no-print">
        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i>
            طباعة
        </button>
        <button onclick="window.close()" class="close-btn">
            <i class="fas fa-times"></i>
            إغلاق
        </button>
    </div>

    <!-- Print Container -->
    <div class="print-container">
        @yield('print-content')
    </div>

    @push('styles')
    <style>
    /* CSS Variables from Print Settings */
    :root {
        @php
            $cssVars = $printSettings->getCssVariables();
            foreach($cssVars as $key => $value) {
                echo $key . ': ' . $value . ";\n    ";
            }
        @endphp
    }

    /* Print Media Queries */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        @page {
            size: {{ $printSettings->page_size }} {{ $printSettings->page_orientation }};
            margin: {{ $printSettings->margin_top }}mm {{ $printSettings->margin_right }}mm {{ $printSettings->margin_bottom }}mm {{ $printSettings->margin_left }}mm;
        }
        
        body {
            font-family: var(--print-font-family) !important;
            font-size: var(--print-font-size) !important;
            line-height: 1.6;
            color: #000;
        }
        
        .no-print {
            display: none !important;
        }
    }

    /* Professional Print Container */
    .professional-print-container {
        background: white;
        position: relative;
        min-height: 100vh;
        font-family: var(--print-font-family);
        font-size: var(--print-font-size);
        color: #333;
    }

    /* Company Header Styling */
    .company-header {
        background: var(--print-header-bg);
        color: var(--print-header-text);
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid var(--print-primary-color);
        margin-bottom: 20px;
    }

    .company-info-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .company-logo-wrapper .company-logo {
        max-height: 80px;
        max-width: 200px;
        object-fit: contain;
    }

    .company-name {
        font-size: var(--print-header-font-size);
        font-weight: {{ $printSettings->font_bold_headers ? 'bold' : 'normal' }};
        color: var(--print-primary-color);
        margin: 0 0 8px 0;
    }

    .company-address, .company-contact {
        font-size: calc(var(--print-font-size) * 0.9);
        color: var(--print-secondary-color);
        margin: 4px 0;
    }

    .company-contact span {
        margin-right: 15px;
    }

    .print-metadata {
        text-align: left;
        font-size: calc(var(--print-font-size) * 0.85);
        color: var(--print-secondary-color);
    }

    .print-date, .print-by {
        margin: 2px 0;
    }

    /* Separators */
    .header-separator, .footer-separator {
        height: 2px;
        background: linear-gradient(to right, var(--print-primary-color), var(--print-accent-color), var(--print-primary-color));
        margin: 15px 0;
    }

    /* Document Content */
    .document-content {
        padding: 0 20px;
        min-height: 400px;
    }

    /* Table Styling Based on Settings */
    .professional-print-container table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: var(--print-font-size);
    }

    @if($printSettings->table_borders)
    .professional-print-container table,
    .professional-print-container table th,
    .professional-print-container table td {
        border: 1px solid var(--print-border-color);
    }
    @endif

    .professional-print-container table th {
        background-color: var(--print-table-header);
        color: var(--print-header-text);
        padding: 12px 8px;
        text-align: center;
        font-weight: {{ $printSettings->font_bold_headers ? 'bold' : 'normal' }};
    }

    .professional-print-container table td {
        padding: 8px;
        @if($printSettings->table_striped_rows)
        background-color: transparent;
        @endif
    }

    @if($printSettings->table_striped_rows)
    .professional-print-container table tbody tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.05);
    }
    @endif

    /* Table Style Variations */
    @if($printSettings->table_style === 'minimal')
    .professional-print-container table {
        border: none;
    }
    .professional-print-container table th {
        border-bottom: 2px solid var(--print-primary-color);
        background: transparent;
    }
    .professional-print-container table td {
        border: none;
        border-bottom: 1px solid var(--print-border-color);
    }
    @elseif($printSettings->table_style === 'bold')
    .professional-print-container table th {
        background-color: var(--print-primary-color);
        color: white;
        font-weight: bold;
    }
    .professional-print-container table {
        border: 2px solid var(--print-primary-color);
    }
    @endif

    /* Document Title Styling */
    .document-title h3 {
        color: var(--print-primary-color);
        font-size: calc(var(--print-header-font-size) * 0.9);
        font-weight: {{ $printSettings->font_bold_headers ? 'bold' : 'normal' }};
        text-align: center;
        margin: 20px 0;
        padding: 10px;
        border-bottom: 2px solid var(--print-accent-color);
    }

    /* Badge and Status Styling */
    .badge {
        padding: 4px 8px;
        font-size: calc(var(--print-font-size) * 0.8);
        border-radius: 3px;
    }

    .badge-success { background: var(--print-accent-color); color: white; }
    .badge-warning { background: #f39c12; color: white; }
    .badge-info { background: var(--print-secondary-color); color: white; }
    .badge-danger { background: #e74c3c; color: white; }

    /* Currency and Number Formatting */
    .currency-amount {
        font-weight: 500;
        color: var(--print-primary-color);
    }

    /* Footer Styling */
    .document-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 10px 20px;
        border-top: 1px solid var(--print-border-color);
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: calc(var(--print-font-size) * 0.8);
        color: var(--print-secondary-color);
    }

    /* Watermark Styling */
    .print-watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 4rem;
        color: var(--print-watermark-color);
        opacity: var(--print-watermark-opacity);
        pointer-events: none;
        z-index: -1;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Signature Section */
    .signature-section {
        margin-top: 40px;
        padding: 20px 0;
    }

    .signature-box {
        border: 1px solid var(--print-border-color);
        min-height: 80px;
        padding: 10px;
        margin: 10px 0;
    }

    .signature-line {
        border-bottom: 1px solid var(--print-border-color);
        margin: 30px 0 8px 0;
        min-height: 20px;
    }

    /* QR Code Styling (if enabled) */
    .qr-code-section {
        text-align: center;
        margin: 20px 0;
        padding: 15px;
        border: 1px solid var(--print-border-color);
    }

    /* Payment Terms Section */
    .payment-terms {
        background-color: rgba(var(--print-accent-color), 0.1);
        padding: 15px;
        margin: 20px 0;
        border-left: 4px solid var(--print-accent-color);
    }

    /* Notes Section */
    .notes-section {
        background-color: rgba(var(--print-secondary-color), 0.1);
        padding: 15px;
        margin: 20px 0;
        border: 1px dashed var(--print-border-color);
    }

    /* Responsive adjustments */
    @media print {
        .company-header {
            page-break-inside: avoid;
        }
        
        .document-title {
            page-break-after: avoid;
        }
        
        .signature-section {
            page-break-inside: avoid;
            margin-top: 30mm;
        }
        
        .payment-terms, .notes-section {
            page-break-inside: avoid;
        }
        
        table thead {
            display: table-header-group;
        }
        
        table tbody {
            display: table-row-group;
        }
        
        tr {
            page-break-inside: avoid;
        }
    }

    /* RTL Support */
    @if($rtl)
    body {
        direction: rtl;
        text-align: right;
    }

    .company-info-section {
        flex-direction: row-reverse;
    }

    .company-contact span {
        margin-right: 0;
        margin-left: 15px;
    }

    .footer-content {
        direction: rtl;
    }
    @endif
    </style>

    @if($printSettings->enable_watermark && $printSettings->watermark_text)
    <style>
    .print-watermark {
        color: {{ $printSettings->watermark_color }};
        opacity: {{ $printSettings->watermark_opacity / 100 }};
    }
    </style>
    @endif

    <script>
    // Add page numbers
    document.addEventListener('DOMContentLoaded', function() {
        const pageNumbers = document.querySelectorAll('.page-number');
        pageNumbers.forEach(function(element) {
            element.textContent = '1'; // Will be handled by browser during print
        });
    });
    </script>
    @endpush

    @stack('scripts')
</body>
</html> 