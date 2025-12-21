@extends('layouts.app')
@section('title', __('messages.income_statement'))
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
@php
if (!isset($totalDebit)) $totalDebit = 0;
if (!isset($totalCredit)) $totalCredit = 0;
if (!isset($totalBalance)) $totalBalance = 0;
@endphp
<div class="reports-container">
    <!-- Header -->
    <div class="report-card mb-4">
        <div class="report-card-header">
            <h2 class="report-title mb-0">
                <i class="fas fa-chart-line mr-2"></i>@lang('messages.income_statement')
            </h2>
        </div>
        <div class="report-card-body">
            <form method="GET" class="report-filters">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="from" class="form-label">
                            <i class="fas fa-calendar-alt mr-1"></i>@lang('messages.from_date')
                        </label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-2">
                        <label for="to" class="form-label">
                            <i class="fas fa-calendar-check mr-1"></i>@lang('messages.to_date')
                        </label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label">
                            <i class="fas fa-filter mr-1"></i>@lang('messages.account_type')
                        </label>
                        <select name="type" id="type" class="form-select">
                            <option value="">@lang('messages.all')</option>
                            <option value="إيراد" {{ $type == 'إيراد' ? 'selected' : '' }}>@lang('messages.revenues')</option>
                            <option value="مصروف" {{ $type == 'مصروف' ? 'selected' : '' }}>@lang('messages.expenses')</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="parent_id" class="form-label">
                            <i class="fas fa-folder mr-1"></i>@lang('messages.parent_category')
                        </label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">@lang('messages.all')</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $parent_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="currency" class="form-label">
                            <i class="fas fa-coins mr-1"></i>@lang('messages.filter_by_currency')
                        </label>
                        <select name="currency" id="currency" class="form-select">
                            <option value="">@lang('messages.all_currencies')</option>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ isset($currency) && $currency == $curr->code ? 'selected' : '' }}>
                                    {{ $curr->code }} - {{ $curr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                        <a href="{{ Route::localizedRoute('reports.income-statement.excel', request()->only(['from','to','type','parent_id','currency','display_currency'])) }}" class="report-btn report-btn-success ml-2">
                            <i class="fas fa-file-excel mr-2"></i>@lang('messages.export_excel')
                        </a>
                        <a href="{{ Route::localizedRoute('reports.income-statement.pdf', request()->only(['from','to','type','parent_id','currency','display_currency'])) }}" class="report-btn report-btn-danger ml-2">
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
    
    <!-- عرض البيانات بعملة واحدة محددة -->
    @if(isset($displayCurrency) && $displayCurrency && isset($allRowsInDisplayCurrency) && count($allRowsInDisplayCurrency) > 0)
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-coins mr-2"></i>@lang('messages.all_accounts_in_currency'): 
                    <span class="currency-badge currency-badge-primary">{{ $displayCurrency }}</span>
                </h5>
            </div>
            <div class="report-card-body p-0">
                <div class="report-table">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.account_type')</th>
                                <th>@lang('messages.original_currency')</th>
                                <th class="text-end">@lang('messages.debit')</th>
                                <th class="text-end">@lang('messages.credit')</th>
                                <th class="text-end">@lang('messages.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $revenueRows = collect($allRowsInDisplayCurrency)->filter(function($row) {
                                    return in_array($row['type'], ['إيراد', 'revenue']);
                                });
                                $expenseRows = collect($allRowsInDisplayCurrency)->filter(function($row) {
                                    return in_array($row['type'], ['مصروف', 'expense']);
                                });
                            @endphp
                            
                            @if($revenueRows->count() > 0)
                                <tr class="table-success">
                                    <td colspan="6" class="fw-bold">
                                        <i class="fas fa-arrow-up mr-2"></i>@lang('messages.revenues')
                                    </td>
                                </tr>
                                @foreach($revenueRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>
                                        <span class="badge badge-success">إيراد</span>
                                    </td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                    <td class="text-end text-success fw-bold">{{ number_format(abs($row['balance']), 2) }} {{ $displayCurrency }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <th colspan="5">@lang('messages.total_revenues')</th>
                                    <th class="text-end text-success">{{ number_format($revenueInDisplayCurrency, 2) }} {{ $displayCurrency }}</th>
                                </tr>
                            @endif
                            
                            @if($expenseRows->count() > 0)
                                <tr class="table-danger">
                                    <td colspan="6" class="fw-bold">
                                        <i class="fas fa-arrow-down mr-2"></i>@lang('messages.expenses')
                                    </td>
                                </tr>
                                @foreach($expenseRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>
                                        <span class="badge badge-danger">مصروف</span>
                                    </td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                    <td class="text-end text-danger fw-bold">{{ number_format(abs($row['balance']), 2) }} {{ $displayCurrency }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <th colspan="5">@lang('messages.total_expenses')</th>
                                    <th class="text-end text-danger">{{ number_format($expenseInDisplayCurrency, 2) }} {{ $displayCurrency }}</th>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="{{ $netInDisplayCurrency >= 0 ? 'table-success' : 'table-danger' }}">
                                <th colspan="5">
                                    <i class="fas fa-{{ $netInDisplayCurrency >= 0 ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                                    {{ $netInDisplayCurrency >= 0 ? __('messages.net_profit') : __('messages.net_loss') }} ({{ $displayCurrency }})
                                </th>
                                <th class="text-end fw-bold">{{ number_format(abs($netInDisplayCurrency), 2) }} {{ $displayCurrency }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    <!-- عرض كل العملات بشكل منفصل -->
    @if((!isset($displayCurrency) || !$displayCurrency) && isset($rowsByCurrency) && $rowsByCurrency->isNotEmpty())
        @foreach($rowsByCurrency as $currency => $currencyRows)
            <div class="report-card mb-4">
                <div class="report-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-coins mr-2"></i>العملة: 
                        <span class="currency-badge currency-badge-primary">{{ $currency }}</span>
                        @php
                            $currencyInfo = \App\Models\Currency::where('code', $currency)->first();
                        @endphp
                        @if($currencyInfo)
                            - {{ $currencyInfo->name }}
                        @endif
                    </h5>
                </div>
                <div class="report-card-body p-0">
                    <div class="report-table">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('messages.item')</th>
                                    <th>@lang('messages.account_type')</th>
                                    <th class="text-end">@lang('messages.debit')</th>
                                    <th class="text-end">@lang('messages.credit')</th>
                                    <th class="text-end">@lang('messages.balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currencyRevenueRows = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['إيراد', 'revenue']);
                                    });
                                    $currencyExpenseRows = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['مصروف', 'expense']);
                                    });
                                @endphp
                                
                                @if($currencyRevenueRows->count() > 0)
                                    <tr class="table-success">
                                        <td colspan="5" class="fw-bold">
                                            <i class="fas fa-arrow-up mr-2"></i>@lang('messages.revenues')
                                        </td>
                                    </tr>
                                    @foreach($currencyRevenueRows as $row)
                                    <tr>
                                        <td>{{ $row['account']->name }}</td>
                                        <td><span class="badge badge-success">إيراد</span></td>
                                        <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                        <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                        <td class="text-end text-success fw-bold">{{ number_format(abs($row['balance']), 2) }} {{ $currency }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <th colspan="4">@lang('messages.total_revenues')</th>
                                        <th class="text-end text-success">{{ number_format($revenuesByCurrency[$currency] ?? 0, 2) }} {{ $currency }}</th>
                                    </tr>
                                @endif
                                
                                @if($currencyExpenseRows->count() > 0)
                                    <tr class="table-danger">
                                        <td colspan="5" class="fw-bold">
                                            <i class="fas fa-arrow-down mr-2"></i>@lang('messages.expenses')
                                        </td>
                                    </tr>
                                    @foreach($currencyExpenseRows as $row)
                                    <tr>
                                        <td>{{ $row['account']->name }}</td>
                                        <td><span class="badge badge-danger">مصروف</span></td>
                                        <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                        <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format(abs($row['balance']), 2) }} {{ $currency }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <th colspan="4">@lang('messages.total_expenses')</th>
                                        <th class="text-end text-danger">{{ number_format($expensesByCurrency[$currency] ?? 0, 2) }} {{ $currency }}</th>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                @php
                                    $netAmount = ($revenuesByCurrency[$currency] ?? 0) - ($expensesByCurrency[$currency] ?? 0);
                                @endphp
                                <tr class="{{ $netAmount >= 0 ? 'table-success' : 'table-danger' }}">
                                    <th colspan="4">
                                        <i class="fas fa-{{ $netAmount >= 0 ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                                        {{ $netAmount >= 0 ? __('messages.net_profit') : __('messages.net_loss') }} ({{ $currency }})
                                    </th>
                                    <th class="text-end fw-bold">{{ number_format(abs($netAmount), 2) }} {{ $currency }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- المجموع الكلي بكل العملات المتاحة -->
    @if(isset($financialResultsInAllCurrencies) && count($financialResultsInAllCurrencies) > 0 && (!isset($displayCurrency) || !$displayCurrency))
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-globe mr-2"></i>المجموع الكلي (بكل العملات)
                </h5>
            </div>
            <div class="report-card-body">
                <div class="row">
                    @foreach($financialResultsInAllCurrencies as $currencyCode => $totals)
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
                                            <td class="text-success">
                                                <i class="fas fa-arrow-up mr-2"></i>الإيرادات
                                            </td>
                                            <td class="text-end text-success fw-bold">{{ number_format($totals['revenue'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-danger">
                                                <i class="fas fa-arrow-down mr-2"></i>المصروفات
                                            </td>
                                            <td class="text-end text-danger fw-bold">{{ number_format($totals['expense'], 2) }}</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #e9ecef; margin-top: 8px; padding-top: 8px;">
                                            <td class="{{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                <i class="fas fa-{{ $totals['net'] >= 0 ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                                                {{ $totals['net'] >= 0 ? 'ربح صافي' : 'خسارة صافية' }}
                                            </td>
                                            <td class="text-end {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold" style="font-size: 16px;">
                                                {{ number_format(abs($totals['net']), 2) }}
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
    
    @if((!isset($displayCurrency) || !$displayCurrency) && (!isset($rowsByCurrency) || $rowsByCurrency->isEmpty()) && (!isset($allRowsInDisplayCurrency) || count($allRowsInDisplayCurrency) == 0))
        <div class="report-card">
            <div class="report-card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد بيانات لعرضها</p>
            </div>
        </div>
    @endif
</div>
@endsection
