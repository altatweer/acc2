@extends('layouts.app')

@section('title', __('sidebar.ledger'))

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('css/reports.css') }}?v={{ time() }}" rel="stylesheet" />

<style>
/* Ledger Specific Styles */
.ledger-container {
    padding: 20px;
}

.ledger-filters {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ledger-table {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ledger-table table {
    margin-bottom: 0;
}

.ledger-table thead th {
    background: #f8f9fa;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    border-bottom: 2px solid #dee2e6;
}

.ledger-table tbody td {
    vertical-align: middle;
    text-align: center;
}

.ledger-table .text-right {
    text-align: right !important;
}

.ledger-table .currency-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.ledger-summary-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.ledger-summary-card h5 {
    color: white;
    margin-bottom: 15px;
}

.ledger-summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.ledger-summary-item:last-child {
    border-bottom: none;
}

.ledger-summary-item strong {
    font-size: 1.1rem;
}
</style>
@endpush

@section('content')
<div class="reports-container ledger-container">
    <!-- Header -->
    <div class="report-card mb-4">
        <div class="report-card-header">
            <h2 class="report-title">
                <i class="fas fa-book mr-2"></i>دفتر الأستاذ
            </h2>
            <p class="text-muted mb-0">عرض تفصيلي لجميع الحركات المالية للحساب</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="ledger-filters">
        <form method="GET" action="{{ route('ledger.index') }}" id="ledgerForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="account_id" class="form-label">
                        <i class="fas fa-university mr-1"></i>الحساب
                    </label>
                    <select name="account_id" id="account_id" class="form-control select2-account" required>
                        <option value="">-- اختر الحساب --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" 
                                data-code="{{ $account->code }}" 
                                data-name="{{ $account->name }}"
                                {{ $selectedAccount == $account->id ? 'selected' : '' }}>
                                {{ $account->code ? $account->code . ' - ' . $account->name : $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="from" class="form-label">
                        <i class="fas fa-calendar-alt mr-1"></i>من تاريخ <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ $from }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="to" class="form-label">
                        <i class="fas fa-calendar-alt mr-1"></i>إلى تاريخ <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ $to }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="currency" class="form-label">
                        <i class="fas fa-coins mr-1"></i>فلتر العملة
                    </label>
                    <select name="currency" id="currency" class="form-control">
                        <option value="all" {{ !$selectedCurrency || $selectedCurrency == 'all' ? 'selected' : '' }}>كل العملات</option>
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ $selectedCurrency == $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} - {{ $curr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search mr-1"></i>عرض التقرير
                    </button>
                </div>
            </div>
            
            @if($selectedAccount && $from && $to)
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="convert_to_single" id="convert_to_single" value="1" {{ $convertToSingleCurrency ? 'checked' : '' }}>
                        <label class="form-check-label" for="convert_to_single">
                            تحويل جميع العملات لعملة واحدة
                        </label>
                    </div>
                </div>
                <div class="col-md-3 mb-3" id="display_currency_wrapper" style="{{ !$convertToSingleCurrency ? 'display: none;' : '' }}">
                    <label for="display_currency" class="form-label">
                        <i class="fas fa-exchange-alt mr-1"></i>العملة للعرض
                    </label>
                    <select name="display_currency" id="display_currency" class="form-control">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->code }}" {{ $displayCurrency == $curr->code ? 'selected' : '' }}>
                                {{ $curr->code }} - {{ $curr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
        </form>
    </div>

    @if($selectedAccount && $from && $to)
        @php
            $account = $accounts->find($selectedAccount);
        @endphp
        
        <!-- Summary Cards -->
        @if($convertToSingleCurrency && $displayCurrency)
            <!-- Single Currency Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white mb-1">الرصيد الافتتاحي ({{ $displayCurrency }})</h6>
                                <h4 class="text-white mb-0">
                                    {{ number_format($openingBalance, 2) }}
                                </h4>
                            </div>
                            <i class="fas fa-wallet fa-2x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white mb-1">إجمالي المدين ({{ $displayCurrency }})</h6>
                                <h4 class="text-white mb-0">{{ number_format($totalDebit, 2) }}</h4>
                            </div>
                            <i class="fas fa-arrow-up fa-2x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white mb-1">إجمالي الدائن ({{ $displayCurrency }})</h6>
                                <h4 class="text-white mb-0">{{ number_format($totalCredit, 2) }}</h4>
                            </div>
                            <i class="fas fa-arrow-down fa-2x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white mb-1">الرصيد النهائي ({{ $displayCurrency }})</h6>
                                <h4 class="text-white mb-0">
                                    @php
                                        $finalBal = $finalBalance ?? 0;
                                    @endphp
                                    {{ number_format($finalBal, 2) }}
                                </h4>
                            </div>
                            <i class="fas fa-balance-scale fa-2x text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Multi Currency Summary Cards - بطاقات لكل عملة -->
            @foreach($summaryByCurrency as $currency => $summary)
                <div class="mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-coins mr-2"></i>ملخص العملة: {{ $currency }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-white mb-1">الرصيد الافتتاحي</h6>
                                        <h4 class="text-white mb-0">
                                            {{ number_format($summary['opening_balance'], 2) }} {{ $currency }}
                                        </h4>
                                    </div>
                                    <i class="fas fa-wallet fa-2x text-white opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-white mb-1">إجمالي المدين</h6>
                                        <h4 class="text-white mb-0">{{ number_format($summary['total_debit'], 2) }} {{ $currency }}</h4>
                                    </div>
                                    <i class="fas fa-arrow-up fa-2x text-white opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-white mb-1">إجمالي الدائن</h6>
                                        <h4 class="text-white mb-0">{{ number_format($summary['total_credit'], 2) }} {{ $currency }}</h4>
                                    </div>
                                    <i class="fas fa-arrow-down fa-2x text-white opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-white mb-1">الرصيد النهائي</h6>
                                        <h4 class="text-white mb-0">
                                            {{ number_format($summary['final_balance'], 2) }} {{ $currency }}
                                        </h4>
                                    </div>
                                    <i class="fas fa-balance-scale fa-2x text-white opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Action Buttons -->
        <div class="mb-3">
            <a href="{{ route('ledger.index', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf mr-1"></i>تصدير PDF
            </a>
            <a href="{{ route('ledger.index', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i>تصدير Excel
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print mr-1"></i>طباعة
            </button>
        </div>

        <!-- Account Info -->
        <div class="report-card mb-4">
            <div class="report-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle mr-2"></i>معلومات الحساب
                </h5>
            </div>
            <div class="report-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>اسم الحساب:</strong> {{ $account->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>رمز الحساب:</strong> {{ $account->code ?? '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong>نوع الحساب:</strong> {{ $account->type }}
                    </div>
                    <div class="col-md-3">
                        <strong>طبيعة الحساب:</strong> 
                        <span class="badge {{ $account->nature === 'مدين' || $account->nature === 'debit' ? 'badge-primary' : 'badge-success' }}">
                            {{ $account->nature === 'مدين' || $account->nature === 'debit' ? 'مدين' : 'دائن' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        @if($convertToSingleCurrency && $displayCurrency)
            <!-- Single Currency View -->
            <div class="ledger-table">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>التاريخ</th>
                            <th>الوصف</th>
                            <th>العملة الأصلية</th>
                            <th>سعر الصرف</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td colspan="7" class="text-right"><strong>الرصيد الافتتاحي</strong></td>
                            <td class="text-right">
                                @if($openingBalance >= 0)
                                    <strong class="text-success">{{ number_format($openingBalance, 2) }} {{ $displayCurrency }}</strong>
                                @else
                                    <strong class="text-danger">{{ number_format($openingBalance, 2) }} {{ $displayCurrency }}</strong>
                                @endif
                            </td>
                        </tr>
                        @php 
                            $balance = $openingBalance;
                            $index = 1;
                            $method = \App\Models\Setting::getBalanceCalculationMethod();
                        @endphp
                        @foreach($entries as $entry)
                            @php
                                // حساب الرصيد التراكمي بناءً على الإعداد المختار
                                if ($method === 'transaction_nature') {
                                    // المنطق البسيط: المدين - الدائن
                                    $balance += $entry->debit - $entry->credit;
                                } else {
                                    // المنطق التقليدي: يعتمد على طبيعة الحساب
                                    if ($account->nature === 'مدين' || $account->nature === 'debit') {
                                        $balance += $entry->debit - $entry->credit;
                                    } else {
                                        $balance += $entry->credit - $entry->debit;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $index++ }}</td>
                                <td>{{ $entry->journalEntry->date ?? '-' }}</td>
                                <td>{{ $entry->description }}</td>
                                <td>
                                    <span class="currency-badge currency-badge-primary">
                                        {{ $entry->original_currency ?? $entry->currency }}
                                    </span>
                                </td>
                                <td>{{ number_format($entry->exchange_rate ?? 1, 4) }}</td>
                                <td class="text-right">{{ number_format($entry->debit, 2) }}</td>
                                <td class="text-right">{{ number_format($entry->credit, 2) }}</td>
                                <td class="text-right">
                                    @if($balance >= 0)
                                        <strong class="text-success">{{ number_format($balance, 2) }} {{ $displayCurrency }}</strong>
                                    @else
                                        <strong class="text-danger">{{ number_format($balance, 2) }} {{ $displayCurrency }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5">الإجمالي</th>
                            <th class="text-right">{{ number_format($totalDebit, 2) }}</th>
                            <th class="text-right">{{ number_format($totalCredit, 2) }}</th>
                            <th class="text-right">
                                @if($balance >= 0)
                                    <strong class="text-success">{{ number_format($balance, 2) }} {{ $displayCurrency }}</strong>
                                @else
                                    <strong class="text-danger">{{ number_format($balance, 2) }} {{ $displayCurrency }}</strong>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <!-- Multi Currency View -->
            @foreach($entriesByCurrency as $currency => $currencyEntries)
                @if($currencyEntries->count() > 0)
                    <div class="ledger-table mb-4">
                        <div class="report-card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-coins mr-2"></i>العملة: {{ $currency }}
                            </h5>
                        </div>
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-info">
                                    <td colspan="5" class="text-right"><strong>الرصيد الافتتاحي</strong></td>
                                    <td class="text-right">
                                        @php
                                            $openingBal = $openingBalancesByCurrency[$currency] ?? 0;
                                        @endphp
                                        @if($openingBal >= 0)
                                            <strong class="text-success">{{ number_format($openingBal, 2) }} {{ $currency }}</strong>
                                        @else
                                            <strong class="text-danger">{{ number_format($openingBal, 2) }} {{ $currency }}</strong>
                                        @endif
                                    </td>
                                </tr>
                                @php 
                                    $balance = $openingBalancesByCurrency[$currency] ?? 0;
                                    $index = 1;
                                    $currencyTotalDebit = 0;
                                    $currencyTotalCredit = 0;
                                    $method = \App\Models\Setting::getBalanceCalculationMethod();
                                @endphp
                                @foreach($currencyEntries as $entry)
                                    @php
                                        // حساب الرصيد التراكمي بناءً على الإعداد المختار
                                        if ($method === 'transaction_nature') {
                                            // المنطق البسيط: المدين - الدائن
                                            $balance += $entry->debit - $entry->credit;
                                        } else {
                                            // المنطق التقليدي: يعتمد على طبيعة الحساب
                                            if ($account->nature === 'مدين' || $account->nature === 'debit') {
                                                $balance += $entry->debit - $entry->credit;
                                            } else {
                                                $balance += $entry->credit - $entry->debit;
                                            }
                                        }
                                        $currencyTotalDebit += $entry->debit;
                                        $currencyTotalCredit += $entry->credit;
                                    @endphp
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $entry->journalEntry->date ?? '-' }}</td>
                                        <td>{{ $entry->description }}</td>
                                        <td class="text-right">{{ number_format($entry->debit, 2) }}</td>
                                        <td class="text-right">{{ number_format($entry->credit, 2) }}</td>
                                        <td class="text-right">
                                            @if($balance >= 0)
                                                <strong class="text-success">{{ number_format($balance, 2) }} {{ $currency }}</strong>
                                            @else
                                                <strong class="text-danger">{{ number_format($balance, 2) }} {{ $currency }}</strong>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3">الإجمالي ({{ $currency }})</th>
                                    <th class="text-right">{{ number_format($currencyTotalDebit, 2) }}</th>
                                    <th class="text-right">{{ number_format($currencyTotalCredit, 2) }}</th>
                                    <th class="text-right">
                                        @if($balance >= 0)
                                            <strong class="text-success">{{ number_format($balance, 2) }} {{ $currency }}</strong>
                                        @else
                                            <strong class="text-danger">{{ number_format($balance, 2) }} {{ $currency }}</strong>
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endforeach
        @endif
    @else
        <div class="report-card">
            <div class="report-card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">يرجى اختيار حساب لعرض دفتر الأستاذ</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

<script>
$(document).ready(function() {
    // تنسيق عرض الحسابات في Select2
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        const $option = $(account.element);
        const code = $option.data('code') || '';
        const name = $option.data('name') || account.text;
        
        if (code) {
            return $(`
                <div class="account-option">
                    <span class="account-name">${name}</span>
                    <span class="account-code">${code}</span>
                </div>
            `);
        }
        
        return account.text;
    }

    function formatAccountSelection(account) {
        if (!account.id) return account.text;
        
        const $option = $(account.element);
        const code = $option.data('code') || '';
        const name = $option.data('name') || account.text;
        
        if (code) {
            return `${code} - ${name}`;
        }
        
        return account.text;
    }

    // تهيئة Select2 للحسابات
    $('.select2-account').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'ابحث عن الحساب...',
        allowClear: true,
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        },
        templateResult: formatAccountOption,
        templateSelection: formatAccountSelection,
        escapeMarkup: function(markup) { return markup; }
    });

    // إظهار/إخفاء حقل العملة للعرض
    $('#convert_to_single').on('change', function() {
        if ($(this).is(':checked')) {
            $('#display_currency_wrapper').show();
            $('#display_currency').prop('required', true);
        } else {
            $('#display_currency_wrapper').hide();
            $('#display_currency').prop('required', false);
        }
    });
    
    // التحقق من التاريخ عند الإرسال
    $('#ledgerForm').on('submit', function(e) {
        const accountId = $('#account_id').val();
        const from = $('#from').val();
        const to = $('#to').val();
        
        if (accountId && (!from || !to)) {
            e.preventDefault();
            alert('⚠️ يجب تحديد تاريخ البداية وتاريخ النهاية عند اختيار حساب');
            return false;
        }
        
        if (from && to && new Date(to) < new Date(from)) {
            e.preventDefault();
            alert('⚠️ تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية');
            return false;
        }
    });
});
</script>
@endpush
