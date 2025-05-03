@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">تفاصيل الراتب</h1>
            <a href="{{ route('salaries.index') }}" class="btn btn-secondary">عودة للقائمة</a>
            <a href="{{ route('salaries.edit', $salary->id) }}" class="btn btn-primary">تعديل</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الموظف:</strong> {{ $salary->employee->name }} ({{ $salary->employee->employee_number }})
                        </div>
                        <div class="col-md-6">
                            <strong>الراتب الأساسي:</strong> {{ number_format($salary->basic_salary, 2) }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>تاريخ السريان:</strong> {{ $salary->effective_from }}
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الانتهاء:</strong> {{ $salary->effective_to ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>البدلات:</strong>
                            @if(is_array($salary->allowances) && count($salary->allowances))
                                <ul>
                                    @foreach($salary->allowances as $allowance)
                                        <li>{{ $allowance['name'] }}: {{ number_format($allowance['amount'], 2) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span>لا يوجد</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>الخصومات:</strong>
                            @if(is_array($salary->deductions) && count($salary->deductions))
                                <ul>
                                    @foreach($salary->deductions as $deduction)
                                        <li>{{ $deduction['name'] }}: {{ number_format($deduction['amount'], 2) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span>لا يوجد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 