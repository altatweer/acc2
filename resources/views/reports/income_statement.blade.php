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
                            @if(!isset($displayCurrency) || !$displayCurrency)
                                {{-- DEBUG INFO - مؤقت --}}
                                @if(true)
                                    <tr class="bg-warning text-dark">
                                        <td colspan="6">
                                            <strong>🔍 DEBUG:</strong>
                                            @if(isset($rowsByCurrency))
                                                العملات المتاحة: {{ implode(', ', $rowsByCurrency->keys()->toArray()) }}
                                                | عدد الصفوف الكلي: {{ $rowsByCurrency->flatten(1)->count() }}
                                                @foreach($rowsByCurrency as $debugCurrency => $debugRows)
                                                    <br>- {{ $debugCurrency }}: {{ $debugRows->count() }} صف
                                                @endforeach
                                            @else
                                                ❌ rowsByCurrency غير موجود
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                
                                @if(isset($rowsByCurrency) && $rowsByCurrency->isNotEmpty())
                                    @foreach($rowsByCurrency as $currency => $currencyRows)
                                        <!-- عنوان العملة -->
                                        <tr class="bg-light">
                                            <td colspan="6" class="fw-bold">
                                                <i class="fas fa-coins"></i>
                                                {{ $currency }}
                                                @php
                                                    $currencyInfo = \App\Models\Currency::where('code', $currency)->first();
                                                @endphp
                                                @if($currencyInfo)
                                                    - {{ $currencyInfo->name }}
                                                @endif
                                            </td>
                                        </tr>
                                        
                                        @foreach($currencyRows as $row)
                                            @if($row['debit'] != 0 || $row['credit'] != 0 || $row['balance'] != 0)
                                            <tr>
                                                <td>{{ $row['account']->name }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $row['currency'] }}</span>
                                                    @if($row['currency'] !== $row['account']->default_currency)
                                                        <small class="text-muted d-block">حساب: {{ $row['account']->default_currency ?: 'غير محدد' }}</small>
                                                    @endif
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
                                            @endif
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach($rows as $row)
                                        <tr>
                                            <td>{{ $row['account']->name }}</td>
                                            <td>
                                                @if($row['type'] == 'إيراد' || $row['type'] == 'revenue')
                                                    <span class="badge badge-success">إيراد</span>
                                                @elseif($row['type'] == 'مصروف' || $row['type'] == 'expense')
                                                    <span class="badge badge-danger">مصروف</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $row['type'] ?? 'غير محدد' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $row['currency'] }}</span>
                                            </td>
                                            <td class="text-end">{{ number_format(abs($row['debit']), 2) }}</td>
                                            <td class="text-end">{{ number_format(abs($row['credit']), 2) }}</td>
                                            <td class="text-end {{ $row['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format(abs($row['balance']), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                // استخدام المجاميع المحسوبة في Controller
                                $defaultCurrency = \App\Models\Currency::getDefaultCode();
                            @endphp
                            
                            <!-- مجاميع كل عملة -->
                            @if(isset($revenuesByCurrency) && count($revenuesByCurrency) > 0)
                                @foreach($revenuesByCurrency as $currency => $revenueAmount)
                                    <tr>
                                        <th colspan="3">@lang('messages.revenues') ({{ $currency }})</th>
                                        <th colspan="3" class="text-end">{{ number_format($revenueAmount, 2) }}</th>
                                    </tr>
                                @endforeach
                            @endif
                            
                            @if(isset($expensesByCurrency) && count($expensesByCurrency) > 0)
                                @foreach($expensesByCurrency as $currency => $expenseAmount)
                                    <tr>
                                        <th colspan="3">@lang('messages.expenses') ({{ $currency }})</th>
                                        <th colspan="3" class="text-end">{{ number_format($expenseAmount, 2) }}</th>
                                    </tr>
                                @endforeach
                            @endif
                            
                            @if(isset($netByCurrency) && count($netByCurrency) > 0)
                                @foreach($netByCurrency as $currency => $netAmount)
                                    <tr class="{{ $netAmount >= 0 ? 'text-success' : 'text-danger' }}">
                                        <th colspan="3">{{ $netAmount >= 0 ? __('messages.net_profit') : __('messages.net_loss') }} ({{ $currency }})</th>
                                        <th colspan="3" class="text-end">{{ number_format(abs($netAmount), 2) }}</th>
                                    </tr>
                                @endforeach
                            @endif
                        </tfoot>
                    </table>
                </div>
            @endif
            
            <!-- المجموع الكلي بكل العملات المتاحة (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
            @if(isset($financialResultsInAllCurrencies) && count($financialResultsInAllCurrencies) > 0)
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line"></i>
                            المجموع الكلي (بكل العملات)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            @foreach($financialResultsInAllCurrencies as $currencyCode => $totals)
                                <div class="col-md-4">
                                    <div class="card h-100 border-0 {{ $currencyCode == $defaultCurrency ? 'bg-primary text-white' : 'bg-light' }}">
                                        <div class="card-header {{ $currencyCode == $defaultCurrency ? 'bg-primary text-white border-primary' : 'bg-light border-light' }}">
                                            <h6 class="mb-0 text-center">
                                                @php
                                                    $currencyInfo = \App\Models\Currency::where('code', $currencyCode)->first();
                                                @endphp
                                                {{ $currencyInfo ? $currencyInfo->symbol : '' }} {{ $currencyCode }}
                                                @if($currencyCode == $defaultCurrency)
                                                    <small class="badge badge-light text-primary ml-1">افتراضي</small>
                                                @endif
                                            </h6>
                                            @if($currencyInfo)
                                                <small class="d-block text-center opacity-75">{{ $currencyInfo->name }}</small>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="text-success">
                                                        <i class="fas fa-arrow-up"></i> الإيرادات
                                                    </td>
                                                    <td class="text-end text-success font-weight-bold">
                                                        {{ number_format($totals['revenue'], 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-danger">
                                                        <i class="fas fa-arrow-down"></i> المصروفات
                                                    </td>
                                                    <td class="text-end text-danger font-weight-bold">
                                                        {{ number_format($totals['expense'], 2) }}
                                                    </td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td class="{{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                                        <i class="fas fa-{{ $totals['net'] >= 0 ? 'plus' : 'minus' }}-circle"></i>
                                                        {{ $totals['net'] >= 0 ? 'ربح صافي' : 'خسارة صافية' }}
                                                    </td>
                                                    <td class="text-end {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
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
        </div>
    </div>
</div>
@endsection 