@extends('layouts.app')
@section('title', __('messages.expenses_revenues'))
@section('content')
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
    </style>
@endif
<div class="container">
    <h2 class="mb-4">@lang('messages.expenses_revenues')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.expenses-revenues.excel', request()->only(['from','to','type','parent_id'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.expenses-revenues.pdf', request()->only(['from','to','type','parent_id'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> @lang('messages.print')</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-3">
            <label for="currency" class="form-label">@lang('messages.filter_by_currency')</label>
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
        <div class="col-md-12 align-self-end">
            <button type="submit" class="btn btn-primary">@lang('messages.show_report')</button>
        </div>
    </form>
    
    <!-- عرض البيانات بعملة واحدة -->
    @if(isset($displayCurrency) && $displayCurrency && isset($allRowsInDisplayCurrency) && count($allRowsInDisplayCurrency) > 0)
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">@lang('messages.all_accounts_in_currency'): {{ $displayCurrency }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.item')</th>
                                <th>@lang('messages.original_currency')</th>
                                <th>@lang('messages.revenues')</th>
                                <th>@lang('messages.expenses')</th>
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
                                    <td colspan="5" class="fw-bold">@lang('messages.revenues')</td>
                                </tr>
                                
                                @foreach($revenueRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="badge bg-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format(abs($row['balance']), 2) }}</td>
                                    <td>-</td>
                                    <td class="text-end text-success">{{ number_format(abs($row['balance']), 2) }}</td>
                                </tr>
                                @endforeach
                            @endif
                            
                            <!-- المصروفات -->
                            @if($expenseRows->count() > 0)
                                <tr class="bg-light">
                                    <td colspan="5" class="fw-bold">@lang('messages.expenses')</td>
                                </tr>
                                
                                @foreach($expenseRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>
                                        @if($row['original_currency'] != $displayCurrency)
                                            <span class="badge bg-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                        @else
                                            {{ $row['original_currency'] }}
                                        @endif
                                    </td>
                                    <td>-</td>
                                    <td class="text-end">{{ number_format(abs($row['balance']), 2) }}</td>
                                    <td class="text-end text-danger">{{ number_format(abs($row['balance']), 2) }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="2">@lang('messages.subtotal') ({{ $displayCurrency }})</th>
                                <th class="text-end">{{ number_format($revenueInDisplayCurrency, 2) }}</th>
                                <th class="text-end">{{ number_format($expenseInDisplayCurrency, 2) }}</th>
                                <th class="text-end {{ $netInDisplayCurrency >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format(abs($netInDisplayCurrency), 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if(!isset($displayCurrency) || !$displayCurrency)
        <div class="card">
            <div class="card-body p-0">
                            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('messages.item')</th>
                            <th>@lang('messages.currency')</th>
                            <th>@lang('messages.revenues')</th>
                            <th>@lang('messages.expenses')</th>
                            <th>@lang('messages.balance')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // استخدام التجميع من Controller بدلاً من إعادة التجميع
                            if (isset($rowsByCurrency) && $rowsByCurrency->isNotEmpty()) {
                                $groupedRows = $rowsByCurrency;
                            } else {
                                // fallback للعرض العادي
                                $groupedRows = collect($rows)->groupBy(function($row) {
                                    return $row['currency'] ?? 'غير محدد';
                                });
                            }
                        @endphp
                        
                        @if(!isset($displayCurrency) || !$displayCurrency)
                            @forelse($groupedRows as $currencyCode => $currencyRows)
                                <!-- عنوان العملة -->
                                <tr class="bg-light">
                                    <td colspan="5" class="fw-bold">
                                        <i class="fas fa-coins"></i>
                                        {{ $currencyCode }}
                                        @php
                                            $currencyInfo = \App\Models\Currency::where('code', $currencyCode)->first();
                                        @endphp
                                        @if($currencyInfo)
                                            - {{ $currencyInfo->name }}
                                        @endif
                                    </td>
                                </tr>
                                
                                @foreach($currencyRows as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $row['currency'] ?? $currencyCode }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($row['type'], ['إيراد', 'revenue']))
                                            <span class="text-success">{{ number_format(abs($row['balance']), 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($row['type'], ['مصروف', 'expense']))
                                            <span class="text-danger">{{ number_format(abs($row['balance']), 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge {{ in_array($row['type'], ['إيراد', 'revenue']) ? 'badge-success' : 'badge-danger' }}">
                                            {{ in_array($row['type'], ['إيراد', 'revenue']) ? 'إيراد' : 'مصروف' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @php
                                    $currRevenue = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['إيراد', 'revenue']);
                                    })->sum('balance');
                                    
                                    $currExpense = $currencyRows->filter(function($row) {
                                        return in_array($row['type'], ['مصروف', 'expense']);
                                    })->sum('balance');
                                    
                                    $currNet = abs($currRevenue) - abs($currExpense);
                                @endphp
                                
                                <!-- المجموع الفرعي لكل عملة -->
                                <tr class="table-secondary">
                                    <td colspan="2" class="fw-bold">@lang('messages.subtotal') ({{ $currencyCode }})</td>
                                    <td class="fw-bold text-end">{{ number_format(abs($currRevenue), 2) }}</td>
                                    <td class="fw-bold text-end">{{ number_format(abs($currExpense), 2) }}</td>
                                    <td class="fw-bold text-end {{ $currNet >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format(abs($currNet), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">@lang('messages.no_data')</td>
                                </tr>
                            @endforelse
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    <!-- المجموع الكلي بكل العملات المتاحة (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
    @if(isset($financialResultsInAllCurrencies) && (!isset($displayCurrency) || !$displayCurrency))
        <div class="card bg-light mt-4 mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">@lang('messages.grand_total') (@lang('messages.in_all_currencies'))</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($financialResultsInAllCurrencies as $currencyCode => $results)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 {{ $currencyCode == $defaultCurrency ? 'border-primary' : '' }}">
                                <div class="card-header {{ $currencyCode == $defaultCurrency ? 'bg-primary text-white' : 'bg-light' }}">
                                    <h6 class="mb-0">{{ $currencyCode }} {{ $currencyCode == $defaultCurrency ? '(' . __('messages.default_currency') . ')' : '' }}</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered mb-0">
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
    @elseif(!isset($financialResultsInAllCurrencies))
        <!-- المجموع الكلي (النمط القديم) -->
        <div class="card mt-4 mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">@lang('messages.grand_total')</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th>@lang('messages.revenues')</th>
                            <th class="text-end">{{ number_format($totalRevenue, 2) }}</th>
                        </tr>
                        <tr>
                            <th>@lang('messages.expenses')</th>
                            <th class="text-end">{{ number_format($totalExpense, 2) }}</th>
                        </tr>
                        @php $net = abs($totalRevenue) - abs($totalExpense); @endphp
                        <tr class="{{ $net >= 0 ? 'table-success' : 'table-danger' }}">
                            <th>{{ $net >= 0 ? __('messages.net_profit') : __('messages.net_loss') }}</th>
                            <th class="text-end">{{ number_format(abs($net), 2) }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection 