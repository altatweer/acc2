@extends('layouts.app')
@section('title', __('messages.balance_sheet'))
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
    <h2 class="mb-4">@lang('messages.balance_sheet')</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.balance-sheet.excel', request()->only(['from','to'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> @lang('messages.export_excel')</a>
        <a href="{{ Route::localizedRoute('reports.balance-sheet.pdf', request()->only(['from','to'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')</a>
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
    
    <!-- عرض بعملة واحدة محددة -->
    @if(isset($displayCurrency) && $displayCurrency && isset($sectionsInDisplayCurrency))
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">@lang('messages.all_accounts_in_currency'): {{ $displayCurrency }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>@lang('messages.assets')</strong></div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm mb-0">
                                    <tbody>
                                    @if(isset($sectionsInDisplayCurrency['أصل']))
                                        @foreach($sectionsInDisplayCurrency['أصل']['rows'] as $row)
                                            <tr>
                                                <td>{{ $row['account']->name }}</td>
                                                <td>
                                                    @if($row['original_currency'] != $displayCurrency)
                                                        <span class="badge bg-info">{{ $row['original_currency'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">@lang('messages.total') ({{ $displayCurrency }})</th>
                                            <th class="text-end">{{ number_format($sectionsInDisplayCurrency['أصل']['total'] ?? 0, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>@lang('messages.liabilities')</strong></div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm mb-0">
                                    <tbody>
                                    @if(isset($sectionsInDisplayCurrency['خصم']))
                                        @foreach($sectionsInDisplayCurrency['خصم']['rows'] as $row)
                                            <tr>
                                                <td>{{ $row['account']->name }}</td>
                                                <td>
                                                    @if($row['original_currency'] != $displayCurrency)
                                                        <span class="badge bg-info">{{ $row['original_currency'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">@lang('messages.total') ({{ $displayCurrency }})</th>
                                            <th class="text-end">{{ number_format($sectionsInDisplayCurrency['خصم']['total'] ?? 0, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>@lang('messages.equity')</strong></div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm mb-0">
                                    <tbody>
                                    @if(isset($sectionsInDisplayCurrency['حقوق ملكية']))
                                        @foreach($sectionsInDisplayCurrency['حقوق ملكية']['rows'] as $row)
                                            <tr>
                                                <td>{{ $row['account']->name }}</td>
                                                <td>
                                                    @if($row['original_currency'] != $displayCurrency)
                                                        <span class="badge bg-info">{{ $row['original_currency'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">@lang('messages.total') ({{ $displayCurrency }})</th>
                                            <th class="text-end">{{ number_format($sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- المجاميع الإجمالية -->
                <div class="card bg-light mt-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">@lang('messages.summary_in_currency') {{ $displayCurrency }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>@lang('messages.assets')</th>
                                        <td class="text-end">{{ number_format($sectionsInDisplayCurrency['أصل']['total'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('messages.liabilities')</th>
                                        <td class="text-end">{{ number_format($sectionsInDisplayCurrency['خصم']['total'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('messages.equity')</th>
                                        <td class="text-end">{{ number_format($sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>@lang('messages.balance')</th>
                                        <td class="text-end {{ (($sectionsInDisplayCurrency['أصل']['total'] ?? 0) - (($sectionsInDisplayCurrency['خصم']['total'] ?? 0) + ($sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0))) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format(abs(($sectionsInDisplayCurrency['أصل']['total'] ?? 0) - (($sectionsInDisplayCurrency['خصم']['total'] ?? 0) + ($sectionsInDisplayCurrency['حقوق ملكية']['total'] ?? 0))), 2) }}
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
    
    @if(isset($sectionsByCurrency) && count($sectionsByCurrency) > 0 && (!isset($displayCurrency) || !$displayCurrency))        
        @foreach($sectionsByCurrency as $currencyCode => $currencySections)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-coins"></i>
                        العملة: {{ $currencyCode }}
                        @php
                            $currencyInfo = \App\Models\Currency::where('code', $currencyCode)->first();
                        @endphp
                        @if($currencyInfo)
                            - {{ $currencyInfo->name }}
                        @endif
                        <span class="badge badge-light ml-2">{{ count($currencySections) }} أقسام</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light"><strong>@lang('messages.assets')</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-sm mb-0">
                                        <tbody>
                                        @if(isset($currencySections['أصل']))
                                            @foreach($currencySections['أصل']['rows'] as $row)
                                                <tr>
                                                    <td>{{ $row['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end">{{ number_format($sectionTotalsByCurrency[$currencyCode]['أصل'] ?? 0, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light"><strong>@lang('messages.liabilities')</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-sm mb-0">
                                        <tbody>
                                        @if(isset($currencySections['خصم']))
                                            @foreach($currencySections['خصم']['rows'] as $row)
                                                <tr>
                                                    <td>{{ $row['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end">{{ number_format($sectionTotalsByCurrency[$currencyCode]['خصم'] ?? 0, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light"><strong>@lang('messages.equity')</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered table-sm mb-0">
                                        <tbody>
                                        @if(isset($currencySections['حقوق ملكية']))
                                            @foreach($currencySections['حقوق ملكية']['rows'] as $row)
                                                <tr>
                                                    <td>{{ $row['account']->name }}</td>
                                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>@lang('messages.total')</th>
                                                <th class="text-end">{{ number_format($sectionTotalsByCurrency[$currencyCode]['حقوق ملكية'] ?? 0, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @elseif(!isset($displayCurrency) || !$displayCurrency)
        <!-- عرض قديم (غير مصنف حسب العملة) - يظهر فقط عند عدم اختيار عملة عرض واحدة -->
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>@lang('messages.assets')</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                            @foreach($sections['أصل']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>@lang('messages.total')</th>
                                    <th class="text-end">{{ number_format($sections['أصل']['total'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>@lang('messages.liabilities')</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                            @foreach($sections['خصم']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>@lang('messages.total')</th>
                                    <th class="text-end">{{ number_format($sections['خصم']['total'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>@lang('messages.equity')</strong></div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                            @foreach($sections['حقوق ملكية']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['account']->name }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>@lang('messages.total')</th>
                                    <th class="text-end">{{ number_format($sections['حقوق ملكية']['total'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- المجموع الكلي بكل العملات المتاحة (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
    @if(isset($balanceSheetTotalsInAllCurrencies) && (!isset($displayCurrency) || !$displayCurrency))
        <div class="card bg-light mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">@lang('messages.grand_total') (@lang('messages.in_all_currencies'))</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($balanceSheetTotalsInAllCurrencies as $currencyCode => $totals)
                        @if((!isset($displayCurrency)) || ($displayCurrency != $currencyCode))
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 {{ $currencyCode == $defaultCurrency ? 'border-primary' : '' }}">
                                <div class="card-header {{ $currencyCode == $defaultCurrency ? 'bg-primary text-white' : 'bg-light' }}">
                                    <h6 class="mb-0">{{ $currencyCode }} {{ $currencyCode == $defaultCurrency ? '(' . __('messages.default_currency') . ')' : '' }}</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered mb-0">
                                        <tr>
                                            <th>@lang('messages.assets')</th>
                                            <td class="text-end">{{ number_format($totals['assets'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('messages.liabilities')</th>
                                            <td class="text-end">{{ number_format($totals['liabilities'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('messages.equity')</th>
                                            <td class="text-end">{{ number_format($totals['equity'], 2) }}</td>
                                        </tr>
                                        <tr class="table-light">
                                            <th>@lang('messages.balance')</th>
                                            <td class="text-end {{ $totals['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format(abs($totals['balance']), 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 