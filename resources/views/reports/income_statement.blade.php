@extends('layouts.app')
@section('title', __('messages.income_statement'))
@section('content')
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
<div class="container">
    <h2 class="mb-4">@lang('messages.income_statement')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.income-statement.excel', request()->only(['from','to'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.income-statement.pdf', request()->only(['from','to'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> @lang('messages.print')</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3">
            <label for="type" class="form-label">@lang('messages.account_type')</label>
            <select name="type" id="type" class="form-select">
                <option value="">@lang('messages.all')</option>
                <option value="إيراد" {{ $type == 'إيراد' ? 'selected' : '' }}>@lang('messages.revenues')</option>
                <option value="مصروف" {{ $type == 'مصروف' ? 'selected' : '' }}>@lang('messages.expenses')</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="parent_id" class="form-label">@lang('messages.parent_category')</label>
            <select name="parent_id" id="parent_id" class="form-select">
                <option value="">@lang('messages.all')</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ $parent_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="currency" class="form-label">@lang('messages.filter_by_currency')</label>
            <select name="currency" id="currency" class="form-select">
                <option value="">@lang('messages.all_currencies')</option>
                @foreach($currencies as $curr)
                    <option value="{{ $curr->code }}" {{ isset($currency) && $currency == $curr->code ? 'selected' : '' }}>
                        {{ $curr->code }} - {{ $curr->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="display_currency" class="form-label">@lang('messages.display_currency')</label>
            <select name="display_currency" id="display_currency" class="form-select">
                <option value="">@lang('messages.original_currencies')</option>
                @foreach($currencies as $curr)
                    <option value="{{ $curr->code }}" {{ isset($displayCurrency) && $displayCurrency == $curr->code ? 'selected' : '' }}>
                        {{ $curr->code }} - {{ $curr->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 align-self-end">
            <button type="submit" class="btn btn-primary">@lang('messages.show_report')</button>
        </div>
    </form>
    
    <div class="card">
        <div class="card-body p-0">
            <!-- عرض البيانات بعملة واحدة محددة -->
            @if(isset($displayCurrency) && $displayCurrency && isset($allRowsInDisplayCurrency) && count($allRowsInDisplayCurrency) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-success">
                            <tr>
                                <th colspan="6" class="text-center">
                                    <h5 class="mb-0">@lang('messages.all_accounts_in_currency'): {{ $displayCurrency }}</h5>
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.account_type')</th>
                                <th>@lang('messages.original_currency')</th>
                                <th>@lang('messages.debit')</th>
                                <th>@lang('messages.credit')</th>
                                <th>@lang('messages.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // تجميع الصفوف حسب النوع
                                $revenueRows = collect($allRowsInDisplayCurrency)->filter(function($row) {
                                    return in_array($row['type'], ['إيراد', 'revenue']);
                                });
                                
                                $expenseRows = collect($allRowsInDisplayCurrency)->filter(function($row) {
                                    return in_array($row['type'], ['مصروف', 'expense']);
                                });
                            @endphp
                            
                            <!-- الإيرادات -->
                            @if($revenueRows->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="6" class="fw-bold">@lang('messages.revenues')</td>
                                </tr>
                                
                                @foreach($revenueRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>{{ $row['type'] }}</td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="badge bg-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['balance']), 2) }}</td>
                                </tr>
                                @endforeach
                                
                                <tr class="table-light">
                                    <th colspan="5">@lang('messages.total_revenues')</th>
                                    <th class="text-end">{{ number_format($revenueInDisplayCurrency, 2) }}</th>
                                </tr>
                            @endif
                            
                            <!-- المصروفات -->
                            @if($expenseRows->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="6" class="fw-bold">@lang('messages.expenses')</td>
                                </tr>
                                
                                @foreach($expenseRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>{{ $row['type'] }}</td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="badge bg-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                    <td class="text-end">{{ number_format(abs($row['balance']), 2) }}</td>
                                </tr>
                                @endforeach
                                
                                <tr class="table-light">
                                    <th colspan="5">@lang('messages.total_expenses')</th>
                                    <th class="text-end">{{ number_format($expenseInDisplayCurrency, 2) }}</th>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="{{ $netInDisplayCurrency >= 0 ? 'table-success' : 'table-danger' }}">
                                <th colspan="5">
                                    {{ $netInDisplayCurrency >= 0 ? __('messages.net_profit') : __('messages.net_loss') }} ({{ $displayCurrency }})
                                </th>
                                <th class="text-end">{{ number_format(abs($netInDisplayCurrency), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
            
            @if(!isset($displayCurrency) || !$displayCurrency)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.account_type')</th>
                                <th>@lang('messages.currency')</th>
                                <th>@lang('messages.debit')</th>
                                <th>@lang('messages.credit')</th>
                                <th>@lang('messages.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // تجميع الصفوف حسب العملة
                                $rowsByCurrency = collect($rows)->groupBy(function($row) {
                                    return $row['account']->currency ?? 'Unknown';
                                });
                            @endphp
                            
                            @if(!isset($displayCurrency) || !$displayCurrency)
                                @foreach($rowsByCurrency as $currency => $currencyRows)
                                    <!-- عنوان العملة -->
                                    <tr class="bg-light">
                                        <td colspan="6" class="fw-bold">{{ $currency }}</td>
                                    </tr>
                                    
                                    @foreach($currencyRows as $row)
                                    <tr>
                                        <td>{{ $row['account']->name }}</td>
                                        <td>{{ $row['type'] }}</td>
                                        <td>{{ $row['account']->currency ?? 'Unknown' }}</td>
                                        <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                        <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                        <td class="text-end">{{ number_format(abs($row['balance']), 2) }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                // حساب المجاميع لكل عملة
                                $revenueByCurrency = [];
                                $expenseByCurrency = [];
                                $netByCurrency = [];
                                
                                foreach($rowsByCurrency as $currency => $currencyRows) {
                                    $revenueRows = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['إيراد', 'revenue']);
                                    });
                                    
                                    $expenseRows = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['مصروف', 'expense']);
                                    });
                                    
                                    $revenueByCurrency[$currency] = $revenueRows->sum(function($row) {
                                        return abs($row['balance']);
                                    });
                                    
                                    $expenseByCurrency[$currency] = $expenseRows->sum(function($row) {
                                        return abs($row['balance']);
                                    });
                                    
                                    $netByCurrency[$currency] = $revenueByCurrency[$currency] - $expenseByCurrency[$currency];
                                }
                                
                                // العملة الافتراضية للتحويل
                                $defaultCurrency = \App\Models\Currency::getDefaultCode();
                                
                                // تحويل جميع القيم إلى العملة الافتراضية
                                $totalRevenueDefaultCurr = 0;
                                $totalExpenseDefaultCurr = 0;
                                
                                foreach($revenueByCurrency as $currency => $amount) {
                                    if ($currency != $defaultCurrency) {
                                        $totalRevenueDefaultCurr += \App\Helpers\CurrencyHelper::convert($amount, $currency, $defaultCurrency);
                                    } else {
                                        $totalRevenueDefaultCurr += $amount;
                                    }
                                }
                                
                                foreach($expenseByCurrency as $currency => $amount) {
                                    if ($currency != $defaultCurrency) {
                                        $totalExpenseDefaultCurr += \App\Helpers\CurrencyHelper::convert($amount, $currency, $defaultCurrency);
                                    } else {
                                        $totalExpenseDefaultCurr += $amount;
                                    }
                                }
                                
                                $netDefaultCurr = $totalRevenueDefaultCurr - $totalExpenseDefaultCurr;
                            @endphp
                            
                            <!-- مجاميع كل عملة -->
                            @foreach($rowsByCurrency as $currency => $currencyRows)
                                <tr>
                                    <th colspan="3">@lang('messages.revenues') ({{ $currency }})</th>
                                    <th colspan="3" class="text-end">{{ number_format($revenueByCurrency[$currency] ?? 0, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3">@lang('messages.expenses') ({{ $currency }})</th>
                                    <th colspan="3" class="text-end">{{ number_format($expenseByCurrency[$currency] ?? 0, 2) }}</th>
                                </tr>
                                <tr class="{{ ($netByCurrency[$currency] ?? 0) >= 0 ? 'table-success' : 'table-danger' }}">
                                    @if(($netByCurrency[$currency] ?? 0) >= 0)
                                        <th colspan="3">@lang('messages.net_profit') ({{ $currency }})</th>
                                        <th colspan="3" class="text-end">{{ number_format($netByCurrency[$currency] ?? 0, 2) }}</th>
                                    @else
                                        <th colspan="3">@lang('messages.net_loss') ({{ $currency }})</th>
                                        <th colspan="3" class="text-end">{{ number_format(abs($netByCurrency[$currency] ?? 0), 2) }}</th>
                                    @endif
                                </tr>
                            @endforeach
                        </tfoot>
                    </table>
                </div>
            @endif
            
            <!-- المجموع الكلي بكل العملات المتاحة (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
            @if(isset($financialResultsInAllCurrencies) && (!isset($displayCurrency) || !$displayCurrency))
                <div class="card bg-light mt-4 mb-0">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">@lang('messages.grand_total') (@lang('messages.in_all_currencies'))</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($financialResultsInAllCurrencies as $currCode => $results)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 {{ $currCode == $defaultCurrency ? 'border-primary' : '' }}">
                                        <div class="card-header {{ $currCode == $defaultCurrency ? 'bg-primary text-white' : 'bg-light' }}">
                                            <h5 class="mb-0">{{ $currCode }} {{ $currCode == $defaultCurrency ? '(' . __('messages.default_currency') . ')' : '' }}</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm mb-0">
                                                <tr>
                                                    <th>@lang('messages.revenues')</th>
                                                    <td class="text-end">{{ number_format($results['revenue'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('messages.expenses')</th>
                                                    <td class="text-end">{{ number_format($results['expense'], 2) }}</td>
                                                </tr>
                                                <tr class="{{ $results['net'] >= 0 ? 'table-success' : 'table-danger' }}">
                                                    <th>{{ $results['net'] >= 0 ? __('messages.net_profit') : __('messages.net_loss') }}</th>
                                                    <td class="text-end fw-bold">{{ number_format(abs($results['net']), 2) }}</td>
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
        </div>
    </div>
</div>
@endsection 