@extends('layouts.app')
@section('title', __('messages.trial_balance'))
@section('content')
<div class="container">
    <h2 class="mb-4">@lang('messages.trial_balance')</h2>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-2">
            <label for="balance_type" class="form-label">@lang('messages.filter_by_balance')</label>
            <select name="balance_type" id="balance_type" class="form-select">
                <option value="">@lang('messages.all')</option>
                <option value="positive" {{ request('balance_type') == 'positive' ? 'selected' : '' }}>@lang('messages.positive_balance_only')</option>
                <option value="negative" {{ request('balance_type') == 'negative' ? 'selected' : '' }}>@lang('messages.negative_balance_only')</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="currency" class="form-label">@lang('messages.filter_by_currency')</label>
            <select name="currency" id="currency" class="form-select">
                <option value="">@lang('messages.all_currencies')</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->code }}" {{ $selectedCurrency == $currency->code ? 'selected' : '' }}>
                        {{ $currency->code }} - {{ $currency->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="display_currency" class="form-label">@lang('messages.display_currency')</label>
            <select name="display_currency" id="display_currency" class="form-select">
                <option value="">@lang('messages.original_currencies')</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->code }}" {{ isset($displayCurrency) && $displayCurrency == $currency->code ? 'selected' : '' }}>
                        {{ $currency->code }} - {{ $currency->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 align-self-end">
            <button type="submit" class="btn btn-primary">@lang('messages.show_report')</button>
        </div>
    </form>
    
    @if(isset($displayCurrency) && $displayCurrency && isset($allRowsInDisplayCurrency) && count($allRowsInDisplayCurrency) > 0)
        <!-- عرض البيانات محولة لعملة واحدة -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">@lang('messages.all_accounts_in_currency'): {{ $displayCurrency }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.account_code')</th>
                                <th>@lang('messages.account_name')</th>
                                <th>@lang('messages.original_currency')</th>
                                <th>@lang('messages.debit')</th>
                                <th>@lang('messages.credit')</th>
                                <th>@lang('messages.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allRowsInDisplayCurrency as $row)
                            <tr>
                                <td>{{ $row['account']->code }}</td>
                                <td>{{ $row['account']->name }}</td>
                                <td>
                                    @if($row['original_currency'] != $displayCurrency)
                                        <span class="badge bg-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                    @else
                                        {{ $row['original_currency'] }}
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">@lang('messages.grand_total') ({{ $displayCurrency }})</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allRowsInDisplayCurrency, 'debit')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allRowsInDisplayCurrency, 'credit')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allRowsInDisplayCurrency, 'balance')), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if(isset($rowsByCurrency) && $rowsByCurrency->isNotEmpty() && (!isset($displayCurrency) || !$displayCurrency))
        @foreach($rowsByCurrency as $currencyCode => $currencyRows)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">@lang('messages.currency'): {{ $currencyCode }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('messages.account_code')</th>
                                    <th>@lang('messages.account_name')</th>
                                    <th>@lang('messages.currency')</th>
                                    <th>@lang('messages.debit')</th>
                                    <th>@lang('messages.credit')</th>
                                    <th>@lang('messages.balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencyRows as $row)
                                <tr>
                                    <td>{{ $row['account']->code }}</td>
                                    <td>{{ $row['account']->name }}</td>
                                    <td>{{ $row['account']->currency }}</td>
                                    <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3">@lang('messages.subtotal') ({{ $currencyCode }})</th>
                                    <th class="text-end">{{ number_format($totalsByCurrency[$currencyCode]['debit'], 2) }}</th>
                                    <th class="text-end">{{ number_format($totalsByCurrency[$currencyCode]['credit'], 2) }}</th>
                                    <th class="text-end">{{ number_format($totalsByCurrency[$currencyCode]['balance'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        
            <!-- المجموع الكلي معروض بكل العملات (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
    @if(!isset($displayCurrency) || !$displayCurrency)
        <div class="card bg-light mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">@lang('messages.grand_total') (@lang('messages.in_all_currencies'))</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($grandTotalInAllCurrencies as $currencyCode => $totals)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header {{ $currencyCode == $defaultCurrency ? 'bg-primary text-white' : 'bg-light' }}">
                                    <h6 class="mb-0">{{ $currencyCode }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th>@lang('messages.debit')</th>
                                            <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('messages.credit')</th>
                                            <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
                                        </tr>
                                        <tr class="table-light">
                                            <th>@lang('messages.balance')</th>
                                            <td class="text-end {{ $totals['balance'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['balance'], 2) }}</td>
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
    @else
        <div class="alert alert-info">
            @lang('messages.no_transactions')
        </div>
    @endif
</div>
@endsection 