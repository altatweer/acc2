@extends('layouts.app')
@section('title', __('messages.trial_balance'))
@section('content')
<link href="{{ asset('css/reports.css') }}?v={{ time() }}" rel="stylesheet">
<div class="container reports-container">
    <div class="report-card mb-4">
        <div class="report-card-header">
            <h2 class="report-title mb-0">
                <i class="fas fa-balance-scale mr-2"></i>@lang('messages.trial_balance')
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
                    <div class="col-md-2">
                        <label for="balance_type" class="form-label">
                            <i class="fas fa-filter mr-1"></i>@lang('messages.filter_by_balance')
                        </label>
                        <select name="balance_type" id="balance_type" class="form-select">
                            <option value="">@lang('messages.all')</option>
                            <option value="positive" {{ request('balance_type') == 'positive' ? 'selected' : '' }}>@lang('messages.positive_balance_only')</option>
                            <option value="negative" {{ request('balance_type') == 'negative' ? 'selected' : '' }}>@lang('messages.negative_balance_only')</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="currency" class="form-label">
                            <i class="fas fa-coins mr-1"></i>@lang('messages.filter_by_currency')
                        </label>
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
                        <label for="display_currency" class="form-label">
                            <i class="fas fa-eye mr-1"></i>@lang('messages.display_currency')
                        </label>
                        <select name="display_currency" id="display_currency" class="form-select">
                            <option value="">@lang('messages.original_currencies')</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}" {{ isset($displayCurrency) && $displayCurrency == $currency->code ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 align-self-end mt-3">
                        <button type="submit" class="report-btn report-btn-primary">
                            <i class="fas fa-search mr-2"></i>@lang('messages.show_report')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- عرض أسعار الصرف الحالية -->
    @if(isset($exchangeRateDetails) && count($exchangeRateDetails) > 1)
        <div class="exchange-rate-info mb-4">
            <h6 class="mb-3">
                <i class="fas fa-exchange-alt mr-2"></i>
                أسعار الصرف الحالية
            </h6>
            <div class="row">
                @foreach($exchangeRateDetails as $currencyCode => $details)
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="exchange-rate-card {{ $details['is_default'] ? 'is-default' : '' }}">
                            <h6 class="mb-2 {{ $details['is_default'] ? 'text-primary' : '' }}">
                                {{ $details['symbol'] }} {{ $currencyCode }}
                                @if($details['is_default'])
                                    <span class="badge badge-primary">افتراضي</span>
                                @endif
                            </h6>
                            <p class="mb-2 text-muted" style="font-size: 12px;">
                                {{ $details['name'] }}
                            </p>
                            @if(!$details['is_default'])
                                <p class="mb-2" style="font-weight: 600; font-size: 13px;">
                                    1 {{ $currencyCode }} = {{ number_format($details['rate'], 6) }} {{ $defaultCurrency }}
                                </p>
                            @endif
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fas fa-clock"></i> {{ $details['last_updated'] }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    @if(isset($displayCurrency) && $displayCurrency && !empty($allRowsInDisplayCurrency) && count($allRowsInDisplayCurrency) > 0)
        <!-- عرض البيانات محولة لعملة واحدة -->
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="report-title mb-0">
                    <i class="fas fa-coins mr-2"></i>@lang('messages.all_accounts_in_currency'): {{ $displayCurrency }}
                </h5>
                @if(isset($conversionDetails) && count($conversionDetails) > 0)
                    <div class="mt-2">
                        <small class="d-block">
                            <i class="fas fa-exchange-alt mr-1"></i> أسعار الصرف المستخدمة:
                            @foreach($conversionDetails as $detail)
                                <span class="currency-badge currency-badge-info mr-1">
                                    1 {{ $detail['from'] }} = {{ number_format($detail['rate'], 6) }} {{ $detail['to'] }}
                                </span>
                            @endforeach
                            <span class="ml-2 text-muted">
                                <i class="fas fa-clock"></i> {{ $conversionDetails[array_key_first($conversionDetails)]['date'] }}
                            </span>
                        </small>
                    </div>
                @endif
            </div>
            <div class="report-card-body p-0">
                <div class="table-responsive">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>@lang('messages.account_code')</th>
                                <th>@lang('messages.account_name')</th>
                                <th>@lang('messages.original_currency')</th>
                                <th>سعر الصرف المستخدم</th>
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
                                        <span class="currency-badge currency-badge-info">{{ $row['original_currency'] }} → {{ $displayCurrency }}</span>
                                    @else
                                        <span class="currency-badge currency-badge-success">{{ $row['original_currency'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['original_currency'] != $displayCurrency)
                                        <small class="text-muted">{{ number_format($row['exchange_rate_used'], 6) }}</small>
                                    @else
                                        <span class="text-success">1.000000</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                                <td class="text-end {{ $row['balance'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 600;">
                                    {{ number_format($row['balance'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">@lang('messages.grand_total') ({{ $displayCurrency }})</th>
                                <th class="text-end">{{ number_format($displayCurrencyTotals['debit'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'debit')), 2) }}</th>
                                <th class="text-end">{{ number_format($displayCurrencyTotals['credit'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'credit')), 2) }}</th>
                                <th class="text-end">{{ number_format($displayCurrencyTotals['balance'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'balance')), 2) }}</th>
                            </tr>
                            @php
                                $totalDebitDisplay = $displayCurrencyTotals['debit'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'debit'));
                                $totalCreditDisplay = $displayCurrencyTotals['credit'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'credit'));
                                $totalBalanceDisplay = $displayCurrencyTotals['balance'] ?? array_sum(array_column($allRowsInDisplayCurrency, 'balance'));
                                $difference = abs($totalDebitDisplay - $totalCreditDisplay);
                                $isBalanced = $difference < 0.01; // تحمل فرق صغير بسبب التقريب
                            @endphp
                            @if(!$isBalanced)
                            <tr class="bg-warning">
                                <td colspan="7" class="text-center">
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>تحذير:</strong> مجموع المدين ({{ number_format($totalDebitDisplay, 2) }}) ≠ مجموع الدائن ({{ number_format($totalCreditDisplay, 2) }})
                                        - الفرق: {{ number_format($difference, 2) }}
                                    </small>
                                </td>
                            </tr>
                            @else
                            <tr class="bg-success">
                                <td colspan="7" class="text-center">
                                    <small class="text-white">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>ميزان المراجعة متوازن:</strong> مجموع المدين = مجموع الدائن
                                    </small>
                                </td>
                            </tr>
                            @endif
                            <tr class="bg-info">
                                <td colspan="7" class="text-center">
                                    <small class="text-white">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>ملاحظة:</strong> الرصيد الكلي ({{ number_format($totalBalanceDisplay, 2) }}) = مجموع الأرصدة الفردية للحسابات
                                        <br>
                                        <small style="font-size: 11px;">(وليس الفرق بين مجموع المدين ومجموع الدائن، لأن كل حساب له طبيعة مختلفة: مدين أو دائن)</small>
                                    </small>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if(isset($rowsByCurrency) && $rowsByCurrency->isNotEmpty() && (!isset($displayCurrency) || !$displayCurrency))
        @if($rowsByCurrency && $rowsByCurrency->count() > 0)
            @foreach($rowsByCurrency as $currency => $currencyRows)
                <div class="report-card mb-4">
                    <div class="report-card-header">
                        <h5 class="report-title mb-0">
                            <i class="fas fa-coins mr-2"></i>
                            العملة: {{ $currency }}
                            @php
                                $currencyInfo = \App\Models\Currency::where('code', $currency)->first();
                            @endphp
                            @if($currencyInfo)
                                - {{ $currencyInfo->name }}
                            @endif
                            <span class="badge badge-light ml-2">{{ $currencyRows->count() }} حساب</span>
                        </h5>
                    </div>
                    <div class="report-card-body p-0">
                        <div class="table-responsive">
                            <table class="report-table">
                                <thead>
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
                                        <td>
                                            <span class="currency-badge currency-badge-primary">{{ $row['currency'] }}</span>
                                            @if($row['currency'] !== $currency)
                                                <small class="text-muted d-block">الحساب: {{ $row['account']->default_currency ?: 'غير محدد' }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                                        <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                                        <td class="text-end {{ $row['balance'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 600;">
                                            {{ number_format($row['balance'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">@lang('messages.total') ({{ $currency }})</th>
                                        <th class="text-end">{{ number_format($totalsByCurrency[$currency]['debit'] ?? 0, 2) }}</th>
                                        <th class="text-end">{{ number_format($totalsByCurrency[$currency]['credit'] ?? 0, 2) }}</th>
                                        <th class="text-end">{{ number_format($totalsByCurrency[$currency]['balance'] ?? 0, 2) }}</th>
                                    </tr>
                                    @php
                                        $totalDebitCurr = $totalsByCurrency[$currency]['debit'] ?? 0;
                                        $totalCreditCurr = $totalsByCurrency[$currency]['credit'] ?? 0;
                                        $totalBalanceCurr = $totalsByCurrency[$currency]['balance'] ?? 0;
                                        $differenceCurr = abs($totalDebitCurr - $totalCreditCurr);
                                        $isBalancedCurr = $differenceCurr < 0.01;
                                    @endphp
                                    @if(!$isBalancedCurr)
                                    <tr class="bg-warning">
                                        <td colspan="6" class="text-center">
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>تحذير:</strong> مجموع المدين ({{ number_format($totalDebitCurr, 2) }}) ≠ مجموع الدائن ({{ number_format($totalCreditCurr, 2) }})
                                                - الفرق: {{ number_format($differenceCurr, 2) }}
                                            </small>
                                        </td>
                                    </tr>
                                    @else
                                    <tr class="bg-success">
                                        <td colspan="6" class="text-center">
                                            <small class="text-white">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>متوازن:</strong> مجموع المدين = مجموع الدائن
                                            </small>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr class="bg-info">
                                        <td colspan="6" class="text-center">
                                            <small class="text-white" style="font-size: 11px;">
                                                <i class="fas fa-info-circle"></i>
                                                الرصيد الكلي ({{ number_format($totalBalanceCurr, 2) }}) = مجموع الأرصدة الفردية
                                                (كل حساب حسب طبيعته: مدين أو دائن)
                                            </small>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        
            <!-- المجموع الكلي معروض بكل العملات (يظهر فقط عند عدم اختيار عملة عرض واحدة) -->
    @if(!isset($displayCurrency) || !$displayCurrency)
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="report-title mb-0">
                    <i class="fas fa-chart-pie mr-2"></i>@lang('messages.grand_total') (@lang('messages.in_all_currencies'))
                </h5>
            </div>
            <div class="report-card-body">
                <div class="row">
                    @foreach($grandTotalInAllCurrencies as $currencyCode => $totals)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="summary-card {{ $currencyCode == $defaultCurrency ? 'border-primary' : '' }}">
                                <div class="summary-card-header">
                                    {{ $currencyCode }}
                                    @if($currencyCode == $defaultCurrency)
                                        <span class="badge badge-primary">افتراضي</span>
                                    @endif
                                </div>
                                <table class="table table-sm table-borderless mb-0 mt-2">
                                    <tr>
                                        <th>@lang('messages.debit')</th>
                                        <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>@lang('messages.credit')</th>
                                        <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <th>@lang('messages.balance')</th>
                                        <td class="text-end summary-card-value {{ $totals['balance'] >= 0 ? 'positive' : 'negative' }}">
                                            {{ number_format($totals['balance'], 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @elseif((!isset($displayCurrency) || !$displayCurrency) && (!isset($rowsByCurrency) || $rowsByCurrency->isEmpty()))
        <div class="report-alert report-alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            @lang('messages.no_transactions')
        </div>
    @elseif(isset($displayCurrency) && $displayCurrency && (empty($allRowsInDisplayCurrency) || count($allRowsInDisplayCurrency) == 0))
        <div class="report-alert report-alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            لا توجد بيانات للعرض بعملة {{ $displayCurrency }}. يرجى التحقق من:
            <ul class="mt-2 mb-0">
                <li>وجود حركات مالية في الفترة المحددة</li>
                <li>وجود أسعار صرف صحيحة للعملات</li>
                <li>اختيار فترة تاريخية صحيحة</li>
            </ul>
        </div>
    @endif
</div>
@endsection 