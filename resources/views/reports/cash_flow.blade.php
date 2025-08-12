@extends('layouts.app')
@section('title', 'تقرير التدفقات النقدية متعددة العملات')

@push('styles')
<style>
.cash-flow-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.cash-flow-card:hover {
    transform: translateY(-2px);
}

.inflow-badge {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.outflow-badge {
    background: linear-gradient(45deg, #dc3545, #fd7e14);
    color: white;
}

.net-positive {
    color: #28a745;
    font-weight: bold;
}

.net-negative {
    color: #dc3545;
    font-weight: bold;
}

.balance-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
}

.transaction-row {
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}

.transaction-row:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
}

.currency-summary {
    background: linear-gradient(45deg, #e3f2fd, #bbdefb);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.flow-indicator {
    font-size: 1.2em;
    font-weight: bold;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-money-bill-wave text-success"></i>
        تقرير التدفقات النقدية متعددة العملات
    </h2>
    
    <!-- فلاتر التقرير -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="from" class="form-label">من تاريخ</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2">
                    <label for="currency" class="form-label">فلترة حسب العملة</label>
                    <select name="currency" id="currency" class="form-control">
                        <option value="">جميع العملات</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}" {{ $selectedCurrency == $currency->code ? 'selected' : '' }}>
                                {{ $currency->code }} - {{ $currency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="display_currency" class="form-label">عملة العرض</label>
                    <select name="display_currency" id="display_currency" class="form-control">
                        <option value="">العملات الأصلية</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}" {{ $displayCurrency == $currency->code ? 'selected' : '' }}>
                                {{ $currency->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> عرض التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- الإحصائيات العامة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="balance-card">
                <h4>{{ $statistics['total_currencies'] }}</h4>
                <p class="mb-0">عدد العملات</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="balance-card">
                <h4>{{ $statistics['total_cash_accounts'] }}</h4>
                <p class="mb-0">الصناديق النقدية</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="balance-card">
                <h4>{{ $statistics['period_days'] }}</h4>
                <p class="mb-0">عدد الأيام</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="balance-card">
                <h4>{{ \Carbon\Carbon::parse($from)->format('d/m') }} - {{ \Carbon\Carbon::parse($to)->format('d/m') }}</h4>
                <p class="mb-0">الفترة</p>
            </div>
        </div>
    </div>
    
    @if(isset($displayCurrency) && $displayCurrency && count($allDataInDisplayCurrency) > 0)
        <!-- عرض البيانات محولة لعملة واحدة -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt"></i>
                    التدفقات النقدية بعملة {{ $displayCurrency }}
                </h5>
                @if(count($conversionDetails) > 0)
                    <small class="d-block mt-1">
                        أسعار الصرف المستخدمة:
                        @foreach($conversionDetails as $detail)
                            <span class="badge badge-light mr-1">
                                1 {{ $detail['from'] }} = {{ number_format($detail['rate'], 6) }} {{ $detail['to'] }}
                            </span>
                        @endforeach
                    </small>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>العملة الأصلية</th>
                                <th>سعر الصرف</th>
                                <th>الرصيد الافتتاحي</th>
                                <th>التدفقات الداخلة</th>
                                <th>التدفقات الخارجة</th>
                                <th>صافي التدفق</th>
                                <th>الرصيد الختامي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allDataInDisplayCurrency as $data)
                            <tr>
                                <td>
                                    <strong>{{ $data['original_currency'] }}</strong>
                                    <br><small class="text-muted">{{ $data['currency_name'] }}</small>
                                </td>
                                <td class="text-center">
                                    @if($data['original_currency'] != $displayCurrency)
                                        <small>{{ number_format($data['exchange_rate_used'], 6) }}</small>
                                    @else
                                        <span class="text-success">1.000000</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="{{ $data['opening_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($data['opening_balance'], 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="inflow-badge badge">
                                        +{{ number_format($data['total_inflow'], 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="outflow-badge badge">
                                        -{{ number_format($data['total_outflow'], 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="{{ $data['net_flow'] >= 0 ? 'net-positive' : 'net-negative' }}">
                                        {{ $data['net_flow'] >= 0 ? '+' : '' }}{{ number_format($data['net_flow'], 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $data['closing_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($data['closing_balance'], 2) }}
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">الإجمالي ({{ $displayCurrency }})</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allDataInDisplayCurrency, 'opening_balance')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allDataInDisplayCurrency, 'total_inflow')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allDataInDisplayCurrency, 'total_outflow')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allDataInDisplayCurrency, 'net_flow')), 2) }}</th>
                                <th class="text-end">{{ number_format(array_sum(array_column($allDataInDisplayCurrency, 'closing_balance')), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if(count($cashFlowData) > 0)
        <!-- التدفقات حسب كل عملة -->
        @foreach($cashFlowData as $currencyCode => $data)
            <div class="card cash-flow-card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">
                                <i class="fas fa-coins text-primary"></i>
                                {{ $data['currency_info']->symbol ?? '' }} {{ $currencyCode }} - {{ $data['currency_info']->name ?? $currencyCode }}
                            </h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge badge-info badge-lg">
                                {{ count($data['accounts']) }} صندوق
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- ملخص العملة -->
                    <div class="currency-summary">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <h6 class="text-muted">الرصيد الافتتاحي</h6>
                                <span class="flow-indicator {{ $data['opening_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($data['opening_balance'], 2) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted">التدفق الداخل</h6>
                                <span class="flow-indicator text-success">
                                    +{{ number_format($data['total_inflow'], 2) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted">التدفق الخارج</h6>
                                <span class="flow-indicator text-danger">
                                    -{{ number_format($data['total_outflow'], 2) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted">صافي التدفق</h6>
                                <span class="flow-indicator {{ $data['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $data['net_flow'] >= 0 ? '+' : '' }}{{ number_format($data['net_flow'], 2) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted">الرصيد الختامي</h6>
                                <span class="flow-indicator {{ $data['closing_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($data['closing_balance'], 2) }}
                                </span>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-muted">سعر الصرف</h6>
                                <span class="flow-indicator text-info">
                                    {{ number_format($data['currency_info']->exchange_rate ?? 1, 4) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- تفاصيل كل صندوق -->
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>الصندوق</th>
                                    <th>رصيد افتتاحي</th>
                                    <th>تدفق داخل</th>
                                    <th>تدفق خارج</th>
                                    <th>صافي التدفق</th>
                                    <th>رصيد ختامي</th>
                                    <th>عدد المعاملات</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['accounts'] as $accountData)
                                <tr class="transaction-row">
                                    <td>
                                        <strong>{{ $accountData['account']->name }}</strong>
                                        <br><small class="text-muted">{{ $accountData['account']->code }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="{{ $accountData['opening_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($accountData['opening_balance'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-success">+{{ number_format($accountData['inflow'], 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-danger">-{{ number_format($accountData['outflow'], 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="{{ $accountData['net_flow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $accountData['net_flow'] >= 0 ? '+' : '' }}{{ number_format($accountData['net_flow'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="{{ $accountData['closing_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($accountData['closing_balance'], 2) }}
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $accountData['transactions_count'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($accountData['transactions_count'] > 0)
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="toggleTransactions('{{ $accountData['account']->id }}')">
                                                <i class="fas fa-list"></i> التفاصيل
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- تفاصيل المعاملات (مخفية افتراضياً) -->
                                @if($accountData['transactions_count'] > 0)
                                <tr id="transactions-{{ $accountData['account']->id }}" style="display: none;">
                                    <td colspan="8">
                                        <div class="p-3 bg-light">
                                            <h6>تفاصيل المعاملات:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>التاريخ</th>
                                                            <th>الوصف</th>
                                                            <th>مدين</th>
                                                            <th>دائن</th>
                                                            <th>رقم القيد</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($accountData['transactions']->take(10) as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->journalEntry->date ?? 'N/A' }}</td>
                                                            <td>{{ $transaction->description }}</td>
                                                            <td class="text-success">
                                                                {{ $transaction->debit > 0 ? number_format($transaction->debit, 2) : '-' }}
                                                            </td>
                                                            <td class="text-danger">
                                                                {{ $transaction->credit > 0 ? number_format($transaction->credit, 2) : '-' }}
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('journal-entries.show', $transaction->journal_entry_id) }}" 
                                                                   class="btn btn-xs btn-outline-primary">
                                                                    {{ $transaction->journal_entry_id }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        @if($accountData['transactions_count'] > 10)
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                ... و {{ $accountData['transactions_count'] - 10 }} معاملة أخرى
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i>
            لا توجد بيانات تدفقات نقدية في الفترة المحددة.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleTransactions(accountId) {
    const element = document.getElementById('transactions-' + accountId);
    if (element.style.display === 'none') {
        element.style.display = 'table-row';
    } else {
        element.style.display = 'none';
    }
}

// Auto-set date range to current month if not set
document.addEventListener('DOMContentLoaded', function() {
    const fromInput = document.getElementById('from');
    const toInput = document.getElementById('to');
    
    if (!fromInput.value) {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        fromInput.value = firstDay.toISOString().split('T')[0];
    }
    
    if (!toInput.value) {
        const now = new Date();
        toInput.value = now.toISOString().split('T')[0];
    }
});
</script>
@endpush 