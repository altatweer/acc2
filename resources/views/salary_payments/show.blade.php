@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">تفاصيل دفعة الراتب</h1>
            <a href="{{ route('salary-payments.index') }}" class="btn btn-secondary">عودة للقائمة</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الموظف:</strong> {{ $salaryPayment->employee->name }} ({{ $salaryPayment->employee->employee_number }})
                        </div>
                        <div class="col-md-6">
                            <strong>الشهر:</strong> {{ $salaryPayment->salary_month }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الراتب الأساسي:</strong> {{ number_format($salaryPayment->gross_salary, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>الصافي:</strong> {{ number_format($salaryPayment->net_salary, 2) }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>البدلات:</strong> {{ number_format($salaryPayment->total_allowances, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>الخصومات:</strong> {{ number_format($salaryPayment->total_deductions, 2) }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>تاريخ الدفع:</strong> {{ $salaryPayment->payment_date }}
                        </div>
                        <div class="col-md-6">
                            <strong>الحالة:</strong> {{ $salaryPayment->status }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 