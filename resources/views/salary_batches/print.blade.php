@extends('layouts.print')

@section('print-content')
<div class="document-title">
    <h3>كشف رواتب شهر {{ $salaryBatch->month }}</h3>
</div>

<div class="document-info">
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>الشهر:</strong>
            <span class="badge badge-primary">{{ $salaryBatch->month }}</span>
        </div>
        <div class="col-md-6">
            <strong>حالة الكشف:</strong>
            <span class="badge badge-{{ $salaryBatch->status === 'approved' ? 'success' : 'warning' }}">
                @if($salaryBatch->status === 'approved')
                    <i class="fas fa-check-circle"></i> معتمد
                @else
                    <i class="fas fa-clock"></i> معلق
                @endif
            </span>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>تاريخ الإنشاء:</strong>
            <span class="currency-amount">{{ $salaryBatch->created_at ? $salaryBatch->created_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
        </div>
        <div class="col-md-6">
            <strong>منشئ الكشف:</strong>
            <span>{{ $salaryBatch->creator->name ?? 'غير محدد' }}</span>
        </div>
    </div>
    
    @if($salaryBatch->status === 'approved')
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>تاريخ الاعتماد:</strong>
            <span class="currency-amount">{{ $salaryBatch->approved_at && is_object($salaryBatch->approved_at) ? $salaryBatch->approved_at->format('Y-m-d H:i') : ($salaryBatch->approved_at ?? 'غير محدد') }}</span>
        </div>
        <div class="col-md-6">
            <strong>معتمد بواسطة:</strong>
            <span>{{ $salaryBatch->approver->name ?? 'غير محدد' }}</span>
        </div>
    </div>
    @endif
    
    <!-- Statistics Section -->
    <div class="row">
        <div class="col-12">
            <div class="statistics-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statistics['total_employees'] }}</div>
                        <div class="stat-label">إجمالي الموظفين</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statistics['paid_employees'] }}</div>
                        <div class="stat-label">تم الدفع</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statistics['pending_employees'] }}</div>
                        <div class="stat-label">في الانتظار</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins text-info"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statistics['total_currencies'] }}</div>
                        <div class="stat-label">عدد العملات</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Salary Details by Currency -->
@foreach($paymentsByCurrency as $currency => $payments)
<div class="currency-section mb-5">
    <h4 class="section-title">
        <i class="fas fa-money-bill-wave text-success"></i> 
        رواتب بعملة {{ $currency }}
        <span class="badge badge-info">{{ $payments->count() }} موظف</span>
    </h4>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">اسم الموظف</th>
                    <th width="12%">رقم الموظف</th>
                    <th width="12%">الراتب الأساسي</th>
                    <th width="12%">البدلات</th>
                    <th width="12%">الخصومات</th>
                    <th width="12%">صافي الراتب</th>
                    <th width="10%">الحالة</th>
                    <th width="5%">العملة</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach($payments as $payment)
                <tr>
                    <td class="text-center">{{ $index++ }}</td>
                    <td>
                        <strong>{{ $payment->employee->name }}</strong>
                        @if($payment->employee->department)
                            <br><small class="text-muted">{{ $payment->employee->department }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-secondary">{{ $payment->employee->employee_number }}</span>
                    </td>
                    <td class="text-right">
                        <span class="currency-amount">{{ number_format($payment->gross_salary, 2) }}</span>
                    </td>
                    <td class="text-right">
                        <span class="text-success currency-amount">{{ number_format($payment->total_allowances, 2) }}</span>
                    </td>
                    <td class="text-right">
                        <span class="text-danger currency-amount">{{ number_format($payment->total_deductions, 2) }}</span>
                    </td>
                    <td class="text-right">
                        <strong class="currency-amount text-primary">{{ number_format($payment->net_salary, 2) }}</strong>
                    </td>
                    <td class="text-center">
                        @if($payment->status === 'paid')
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> مدفوع
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> معلق
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ $currency }}</span>
                    </td>
                </tr>
                @endforeach
                
                <!-- Currency Totals -->
                <tr class="table-total">
                    <td class="text-center"><i class="fas fa-calculator text-primary"></i></td>
                    <td colspan="2"><strong>إجمالي {{ $currency }}</strong></td>
                    <td class="text-right">
                        <strong class="currency-amount">{{ number_format($totalsByCurrency[$currency]['gross'], 2) }}</strong>
                    </td>
                    <td class="text-right">
                        <strong class="text-success currency-amount">{{ number_format($totalsByCurrency[$currency]['allowances'], 2) }}</strong>
                    </td>
                    <td class="text-right">
                        <strong class="text-danger currency-amount">{{ number_format($totalsByCurrency[$currency]['deductions'], 2) }}</strong>
                    </td>
                    <td class="text-right">
                        <strong class="text-primary currency-amount">{{ number_format($totalsByCurrency[$currency]['net'], 2) }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary">{{ $totalsByCurrency[$currency]['count'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary">{{ $currency }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach

<!-- Summary Section -->
<div class="summary-section mb-4">
    <div class="alert alert-info text-center">
        <h5 class="mb-3">
            <i class="fas fa-chart-bar text-primary"></i>
            ملخص كشف الرواتب
        </h5>
        <div class="row">
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number">{{ $statistics['total_employees'] }}</div>
                    <div class="summary-label">إجمالي الموظفين</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number text-success">{{ $statistics['paid_employees'] }}</div>
                    <div class="summary-label">تم الدفع</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number text-warning">{{ $statistics['pending_employees'] }}</div>
                    <div class="summary-label">في الانتظار</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-item">
                    <div class="summary-number text-info">{{ $statistics['total_currencies'] }}</div>
                    <div class="summary-label">عدد العملات</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Signature Section -->
<div class="signature-section page-break-inside-avoid">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">مدير الموارد البشرية</div>
        <div class="signature-name"></div>
    </div>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">المدير المالي</div>
        <div class="signature-name"></div>
    </div>
    
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">المدير العام</div>
        <div class="signature-name"></div>
    </div>
</div>

<!-- Additional Styles -->
<style>
.section-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.currency-section {
    page-break-inside: avoid;
}

.statistics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-top: 15px;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 24px;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.summary-section .alert {
    border: 2px solid #3498db;
    border-radius: 10px;
}

.summary-item {
    text-align: center;
    padding: 10px;
}

.summary-number {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.summary-label {
    font-size: 14px;
    color: #6c757d;
}

.signature-name {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    min-height: 15px;
}

@media print {
    .section-title {
        font-size: 16px;
        margin-bottom: 15px;
        padding-bottom: 8px;
    }
    
    .statistics-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .stat-card {
        padding: 10px;
    }
    
    .stat-number {
        font-size: 18px;
    }
    
    .summary-number {
        font-size: 20px;
    }
    
    .signature-section {
        margin-top: 40mm;
    }
    
    .signature-box {
        min-height: 25mm;
    }
    
    .signature-line {
        margin: 20mm 0 5mm 0;
    }
    
    /* Ensure proper page breaks */
    .currency-section {
        page-break-inside: avoid;
    }
    
    .table thead {
        display: table-header-group;
    }
    
    .table tbody {
        display: table-row-group;
    }
}
</style>
@endsection 