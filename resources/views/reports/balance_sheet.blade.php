@extends('layouts.app')
@section('title', __('messages.balance_sheet'))
@section('content')
<link href="{{ asset('css/reports.css') }}?v={{ time() }}" rel="stylesheet">
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
@endif
<div class="reports-container">
    <!-- Header -->
    <div class="report-card mb-4">
        <div class="report-card-header">
            <h2 class="report-title mb-0">
                <i class="fas fa-file-invoice-dollar mr-2"></i>@lang('messages.balance_sheet')
            </h2>
        </div>
        <div class="report-card-body">
            <form method="GET" class="report-filters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="from" class="form-label">
                            <i class="fas fa-calendar-alt mr-1"></i>@lang('messages.from_date')
                        </label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label">
                            <i class="fas fa-calendar-check mr-1"></i>@lang('messages.to_date')
                        </label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-3">
                        <label for="currency" class="form-label">
                            <i class="fas fa-coins mr-1"></i>@lang('messages.filter_by_currency')
                        </label>
                        <select name="currency" id="currency" class="form-select">
                            <option value="">@lang('messages.all_currencies')</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ isset($selectedCurrency) && $selectedCurrency == $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="display_currency" class="form-label">
                            <i class="fas fa-eye mr-1"></i>@lang('messages.display_currency')
                        </label>
                        <select name="display_currency" id="display_currency" class="form-select">
                            <option value="">@lang('messages.original_currencies')</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ isset($displayCurrency) && $displayCurrency == $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 align-self-end mt-3">
                        <button type="submit" class="report-btn report-btn-primary">
                            <i class="fas fa-search mr-2"></i>@lang('messages.show_report')
                        </button>
                        <a href="{{ Route::localizedRoute('reports.balance-sheet.excel', request()->only(['from','to','currency','display_currency'])) }}" class="report-btn report-btn-success ml-2">
                            <i class="fas fa-file-excel mr-2"></i>@lang('messages.export_excel')
                        </a>
                        <a href="{{ Route::localizedRoute('reports.balance-sheet.pdf', request()->only(['from','to','currency','display_currency'])) }}" class="report-btn report-btn-danger ml-2">
                            <i class="fas fa-file-pdf mr-2"></i>@lang('messages.export_pdf')
                        </a>
                        <button onclick="window.print()" class="report-btn report-btn-info ml-2">
                            <i class="fas fa-print mr-2"></i>@lang('messages.print')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- عرض بعملة واحدة محددة -->
    @if(isset($displayCurrency) && $displayCurrency && isset($sectionsInDisplayCurrency))
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-coins mr-2"></i>@lang('messages.all_accounts_in_currency'): 
                    <span class="currency-badge currency-badge-primary">{{ $displayCurrency }}</span>
                </h5>
            </div>
            <div class="report-card-body">
                <div class="row">
                    <!-- الأصول -->
                    <div class="col-md-4 mb-4">
                        <div class="balance-sheet-section">
                            <div class="balance-sheet-section-header assets-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-arrow-up mr-2"></i>
                                    @lang('messages.assets')
                                </h5>
                            </div>
                            <div class="balance-sheet-section-body">
                                <div class="balance-sheet-table">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="account-col">الحساب</th>
                                                <th class="balance-col text-end">الرصيد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($sectionsInDisplayCurrency['أصل']))
                                            @foreach($sectionsInDisplayCurrency['أصل']['rows'] as $row)
                                                <tr>
                                                    <td class="account-name">
                                                        {{ $row['account']->name }}
                                                        @if($row['original_currency'] != $displayCurrency)
                                                            <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $displayCurrency }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="section-total">
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end total-value">{{ number_format($sectionsInDisplayCurrency['أصل']['total'] ?? 0, 2) }} {{ $displayCurrency }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- الخصوم -->
                    <div class="col-md-4 mb-4">
                        <div class="balance-sheet-section">
                            <div class="balance-sheet-section-header liabilities-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-arrow-down mr-2"></i>
                                    @lang('messages.liabilities')
                                </h5>
                            </div>
                            <div class="balance-sheet-section-body">
                                <div class="balance-sheet-table">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="account-col">الحساب</th>
                                                <th class="balance-col text-end">الرصيد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($sectionsInDisplayCurrency['خصم']))
                                            @foreach($sectionsInDisplayCurrency['خصم']['rows'] as $row)
                                                <tr>
                                                    <td class="account-name">
                                                        {{ $row['account']->name }}
                                                        @if($row['original_currency'] != $displayCurrency)
                                                            <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $displayCurrency }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="section-total">
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end total-value">{{ number_format($sectionsInDisplayCurrency['خصم']['total'] ?? 0, 2) }} {{ $displayCurrency }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- حقوق الملكية -->
                    <div class="col-md-4 mb-4">
                        <div class="balance-sheet-section">
                            <div class="balance-sheet-section-header equity-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-balance-scale mr-2"></i>
                                    @lang('messages.equity')
                                </h5>
                            </div>
                            <div class="balance-sheet-section-body">
                                <div class="balance-sheet-table">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="account-col">الحساب</th>
                                                <th class="balance-col text-end">الرصيد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($sectionsInDisplayCurrency['حقوق ملكية']))
                                            @foreach($sectionsInDisplayCurrency['حقوق ملكية']['rows'] as $row)
                                                <tr>
                                                    <td class="account-name">
                                                        {{ $row['account']->name }}
                                                        @if($row['original_currency'] != $displayCurrency)
                                                            <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $displayCurrency }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="section-total">
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end total-value">{{ number_format($sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0, 2) }} {{ $displayCurrency }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- المجاميع الإجمالية -->
                @php
                    $assetsTotal = $sectionsInDisplayCurrency['أصل']['total'] ?? 0;
                    $liabilitiesTotal = $sectionsInDisplayCurrency['خصم']['total'] ?? 0;
                    $equityTotal = $sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0;
                    $balance = $assetsTotal - ($liabilitiesTotal + $equityTotal);
                @endphp
                <div class="summary-card mt-4">
                    <div class="report-card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator mr-2 text-primary"></i>@lang('messages.summary_in_currency') {{ $displayCurrency }}
                        </h5>
                    </div>
                    <div class="report-card-body">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <table class="table table-borderless mb-0 balance-summary-table">
                                    <tr>
                                        <td class="summary-label">
                                            <i class="fas fa-arrow-up text-success mr-2"></i>
                                            @lang('messages.assets')
                                        </td>
                                        <td class="text-end summary-value">{{ number_format($assetsTotal, 2) }} {{ $displayCurrency }}</td>
                                    </tr>
                                    <tr>
                                        <td class="summary-label">
                                            <i class="fas fa-arrow-down text-danger mr-2"></i>
                                            @lang('messages.liabilities')
                                        </td>
                                        <td class="text-end summary-value">{{ number_format($liabilitiesTotal, 2) }} {{ $displayCurrency }}</td>
                                    </tr>
                                    <tr>
                                        <td class="summary-label">
                                            <i class="fas fa-balance-scale text-info mr-2"></i>
                                            @lang('messages.equity')
                                        </td>
                                        <td class="text-end summary-value">{{ number_format($equityTotal, 2) }} {{ $displayCurrency }}</td>
                                    </tr>
                                    <tr class="balance-row">
                                        <td class="summary-label fw-bold">
                                            <i class="fas fa-{{ $balance >= 0 ? 'check-circle text-success' : 'exclamation-triangle text-danger' }} mr-2"></i>
                                            @lang('messages.balance')
                                        </td>
                                        <td class="text-end summary-value {{ $balance >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: bold; font-size: 18px;">
                                            {{ number_format(abs($balance), 2) }} {{ $displayCurrency }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- عرض كل العملات بشكل منفصل -->
    @if(isset($sectionsByCurrency) && count($sectionsByCurrency) > 0 && (!isset($displayCurrency) || !$displayCurrency))
        @foreach($sectionsByCurrency as $currencyCode => $currencySections)
            <div class="report-card mb-4">
                <div class="report-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-coins mr-2"></i>العملة: 
                        <span class="currency-badge currency-badge-primary">{{ $currencyCode }}</span>
                        @php
                            $currencyInfo = \App\Models\Currency::where('code', $currencyCode)->first();
                        @endphp
                        @if($currencyInfo)
                            - {{ $currencyInfo->name }}
                        @endif
                    </h5>
                </div>
                <div class="report-card-body">
                    <div class="row">
                        <!-- الأصول -->
                        <div class="col-md-4 mb-4">
                            <div class="balance-sheet-section">
                                <div class="balance-sheet-section-header assets-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-arrow-up mr-2"></i>
                                        @lang('messages.assets')
                                    </h5>
                                </div>
                                <div class="balance-sheet-section-body">
                                    <div class="balance-sheet-table">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="account-col">الحساب</th>
                                                    <th class="balance-col text-end">الرصيد</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($currencySections['أصل']))
                                                @foreach($currencySections['أصل']['rows'] as $row)
                                                    <tr>
                                                        <td class="account-name">{{ $row['account']->name }}</td>
                                                        <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $currencyCode }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                                <tr class="section-total">
                                                    <th>@lang('messages.total')</th>
                                                    <th class="text-end total-value">{{ number_format($sectionTotalsByCurrency[$currencyCode]['أصل'] ?? 0, 2) }} {{ $currencyCode }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- الخصوم -->
                        <div class="col-md-4 mb-4">
                            <div class="balance-sheet-section">
                                <div class="balance-sheet-section-header liabilities-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-arrow-down mr-2"></i>
                                        @lang('messages.liabilities')
                                    </h5>
                                </div>
                                <div class="balance-sheet-section-body">
                                    <div class="balance-sheet-table">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="account-col">الحساب</th>
                                                    <th class="balance-col text-end">الرصيد</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($currencySections['خصم']))
                                                @foreach($currencySections['خصم']['rows'] as $row)
                                                    <tr>
                                                        <td class="account-name">{{ $row['account']->name }}</td>
                                                        <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $currencyCode }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                                <tr class="section-total">
                                                    <th>@lang('messages.total')</th>
                                                    <th class="text-end total-value">{{ number_format($sectionTotalsByCurrency[$currencyCode]['خصم'] ?? 0, 2) }} {{ $currencyCode }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- حقوق الملكية -->
                        <div class="col-md-4 mb-4">
                            <div class="balance-sheet-section">
                                <div class="balance-sheet-section-header equity-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-balance-scale mr-2"></i>
                                        @lang('messages.equity')
                                    </h5>
                                </div>
                                <div class="balance-sheet-section-body">
                                    <div class="balance-sheet-table">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="account-col">الحساب</th>
                                                    <th class="balance-col text-end">الرصيد</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($currencySections['حقوق ملكية']))
                                                @foreach($currencySections['حقوق ملكية']['rows'] as $row)
                                                    <tr>
                                                        <td class="account-name">{{ $row['account']->name }}</td>
                                                        <td class="text-end balance-value">{{ number_format($row['balance'], 2) }} {{ $currencyCode }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                                <tr class="section-total">
                                                    <th>@lang('messages.total')</th>
                                                    <th class="text-end total-value">{{ number_format($sectionTotalsByCurrency[$currencyCode]['حقوق ملكية'] ?? 0, 2) }} {{ $currencyCode }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ملخص هذه العملة -->
                    @php
                        $assetsTotal = $sectionTotalsByCurrency[$currencyCode]['أصل'] ?? 0;
                        $liabilitiesTotal = $sectionTotalsByCurrency[$currencyCode]['خصم'] ?? 0;
                        $equityTotal = $sectionTotalsByCurrency[$currencyCode]['حقوق ملكية'] ?? 0;
                        $balance = $assetsTotal - ($liabilitiesTotal + $equityTotal);
                    @endphp
                    <div class="summary-card mt-3">
                        <div class="report-card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-calculator mr-2 text-primary"></i>ملخص العملة {{ $currencyCode }}
                            </h6>
                        </div>
                        <div class="report-card-body">
                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <table class="table table-borderless mb-0 balance-summary-table">
                                        <tr>
                                            <td class="summary-label">
                                                <i class="fas fa-arrow-up text-success mr-2"></i>
                                                @lang('messages.assets')
                                            </td>
                                            <td class="text-end summary-value">{{ number_format($assetsTotal, 2) }} {{ $currencyCode }}</td>
                                        </tr>
                                        <tr>
                                            <td class="summary-label">
                                                <i class="fas fa-arrow-down text-danger mr-2"></i>
                                                @lang('messages.liabilities')
                                            </td>
                                            <td class="text-end summary-value">{{ number_format($liabilitiesTotal, 2) }} {{ $currencyCode }}</td>
                                        </tr>
                                        <tr>
                                            <td class="summary-label">
                                                <i class="fas fa-balance-scale text-info mr-2"></i>
                                                @lang('messages.equity')
                                            </td>
                                            <td class="text-end summary-value">{{ number_format($equityTotal, 2) }} {{ $currencyCode }}</td>
                                        </tr>
                                        <tr class="balance-row">
                                            <td class="summary-label fw-bold">
                                                <i class="fas fa-{{ $balance >= 0 ? 'check-circle text-success' : 'exclamation-triangle text-danger' }} mr-2"></i>
                                                @lang('messages.balance')
                                            </td>
                                            <td class="text-end summary-value {{ $balance >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: bold; font-size: 16px;">
                                                {{ number_format(abs($balance), 2) }} {{ $currencyCode }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- المجموع الكلي بكل العملات المتاحة -->
    @if(isset($balanceSheetTotalsInAllCurrencies) && count($balanceSheetTotalsInAllCurrencies) > 0 && (!isset($displayCurrency) || !$displayCurrency))
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-globe mr-2"></i>@lang('messages.grand_total') (@lang('messages.in_all_currencies'))
                </h5>
            </div>
            <div class="report-card-body">
                <div class="row">
                    @foreach($balanceSheetTotalsInAllCurrencies as $currencyCode => $totals)
                        <div class="col-md-4 mb-4">
                            <div class="summary-card {{ $currencyCode == $defaultCurrency ? 'border-primary' : '' }}">
                                <div class="report-card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-coins mr-2 text-primary"></i>
                                        {{ $currencyCode }} 
                                        @if($currencyCode == $defaultCurrency)
                                            <span class="badge badge-light">افتراضي</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="report-card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>
                                                <i class="fas fa-arrow-up text-success mr-2"></i>
                                                @lang('messages.assets')
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($totals['assets'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-arrow-down text-danger mr-2"></i>
                                                @lang('messages.liabilities')
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($totals['liabilities'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-balance-scale text-info mr-2"></i>
                                                @lang('messages.equity')
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($totals['equity'], 2) }}</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #e9ecef; margin-top: 8px; padding-top: 8px;">
                                            <td class="fw-bold">
                                                <i class="fas fa-{{ $totals['balance'] >= 0 ? 'check-circle text-success' : 'exclamation-triangle text-danger' }} mr-2"></i>
                                                @lang('messages.balance')
                                            </td>
                                            <td class="text-end {{ $totals['balance'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: bold; font-size: 16px;">
                                                {{ number_format(abs($totals['balance']), 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    
    @if((!isset($sectionsByCurrency) || count($sectionsByCurrency) == 0) && (!isset($displayCurrency) || !$displayCurrency))
        <div class="report-card">
            <div class="report-card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد بيانات لعرضها</p>
            </div>
        </div>
    @endif
</div>
@endsection
