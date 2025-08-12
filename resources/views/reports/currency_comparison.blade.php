@extends('layouts.app')
@section('title', 'تقرير مقارنة العملات')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.currency-card {
    transition: transform 0.2s ease-in-out;
    border-left: 4px solid;
}

.currency-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.chart-container {
    position: relative;
    height: 400px;
    margin-bottom: 2rem;
}

.stat-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 1rem;
}

.currency-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 15px;
    font-size: 0.9em;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-chart-pie text-primary"></i>
        تقرير مقارنة العملات
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
                <div class="col-md-4">
                    <label for="currencies" class="form-label">العملات المراد مقارنتها</label>
                    <select name="currencies[]" id="currencies" class="form-control" multiple>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}" 
                                    {{ in_array($currency->code, $selectedCurrencies) ? 'selected' : '' }}>
                                {{ $currency->code }} - {{ $currency->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">اختر عملة أو أكثر للمقارنة</small>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> عرض التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @if(count($currencyData) > 0)
        <!-- الإحصائيات العامة -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <h3>{{ $statistics['total_currencies'] }}</h3>
                    <p class="mb-0">عدد العملات</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3>{{ $statistics['total_accounts'] }}</h3>
                    <p class="mb-0">إجمالي الحسابات</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3>{{ number_format($statistics['grand_total'], 2) }}</h3>
                    <p class="mb-0">المجموع ({{ $defaultCurrency }})</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h3>{{ $statistics['largest_currency'] ?? 'N/A' }}</h3>
                    <p class="mb-0">أكبر عملة</p>
                </div>
            </div>
        </div>
        
        <!-- الرسوم البيانية -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie"></i>
                            توزيع الأرصدة حسب العملة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar"></i>
                            مقارنة الأرصدة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- مفتاح الألوان -->
        <div class="currency-legend">
            @foreach($currencyData as $code => $data)
                <div class="legend-item">
                    <div class="legend-color" style="background-color: {{ $data['color'] }}"></div>
                    <span>{{ $code }} - {{ $data['name'] }}</span>
                </div>
            @endforeach
        </div>
        
        <!-- تفاصيل كل عملة -->
        <div class="row">
            @foreach($currencyData as $code => $data)
                <div class="col-md-4 mb-4">
                    <div class="card currency-card h-100" style="border-left-color: {{ $data['color'] }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                {{ $data['symbol'] }} {{ $code }}
                            </h5>
                            <span class="badge badge-primary badge-pill">
                                {{ number_format($data['percentage'], 1) }}%
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted">{{ $data['name'] }}</h6>
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h4 class="text-success mb-0">{{ $data['accounts_count'] }}</h4>
                                    <small class="text-muted">حساب</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-0">{{ number_format($data['exchange_rate'], 4) }}</h4>
                                    <small class="text-muted">سعر الصرف</small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-2">
                                <strong>الرصيد الأصلي:</strong>
                                <span class="float-right {{ $data['total_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($data['total_balance'], 2) }} {{ $code }}
                                </span>
                            </div>
                            
                            <div class="mb-2">
                                <strong>المعادل بـ {{ $defaultCurrency }}:</strong>
                                <span class="float-right {{ $data['total_balance_default'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($data['total_balance_default'], 2) }}
                                </span>
                            </div>
                            
                            @if($data['positive_balance'] > 0)
                                <div class="mb-2">
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i>
                                        موجب: {{ number_format($data['positive_balance'], 2) }} {{ $code }}
                                    </small>
                                </div>
                            @endif
                            
                            @if($data['negative_balance'] > 0)
                                <div class="mb-2">
                                    <small class="text-danger">
                                        <i class="fas fa-arrow-down"></i>
                                        سالب: {{ number_format($data['negative_balance'], 2) }} {{ $code }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i>
            لا توجد بيانات للعرض. تأكد من اختيار عملات تحتوي على حسابات نشطة.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// بيانات الرسوم البيانية
const chartData = @json($chartData);
const currencyData = @json($currencyData);

// رسم بياني دائري
const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: chartData.labels,
        datasets: [{
            data: chartData.balances,
            backgroundColor: chartData.colors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const currency = chartData.labels[context.dataIndex];
                        const value = context.parsed;
                        const percentage = ((value / chartData.balances.reduce((a, b) => a + b, 0)) * 100).toFixed(1);
                        return `${currency}: ${value.toLocaleString()} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// رسم بياني عمودي
const barCtx = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: 'الرصيد ({{ $defaultCurrency }})',
            data: chartData.balances,
            backgroundColor: chartData.colors.map(color => color + '80'), // شفافية 50%
            borderColor: chartData.colors,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.parsed.y.toLocaleString()} {{ $defaultCurrency }}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        }
    }
});

// تحسين select متعدد الخيارات
document.getElementById('currencies').addEventListener('change', function() {
    if (this.selectedOptions.length === 0) {
        // إذا لم يتم اختيار شيء، اختر جميع العملات
        Array.from(this.options).forEach(option => option.selected = true);
    }
});
</script>
@endpush 