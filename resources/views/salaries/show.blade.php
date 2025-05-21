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
                <div class="card-header">
                    <h3 class="card-title">بيانات الراتب الحالي</h3>
                </div>
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
                            <strong>تاريخ الانتهاء:</strong> {{ $salary->effective_to ?? 'غير محدد' }}
                        </div>
                    </div>
                    
                    @if(is_array($salary->allowances) && count($salary->allowances))
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>البدلات:</strong>
                                <ul class="list-unstyled">
                                    @foreach($salary->allowances as $allowance)
                                        <li><span class="badge badge-success">{{ $allowance['name'] }}: {{ number_format($allowance['amount'], 2) }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if(is_array($salary->deductions) && count($salary->deductions))
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>الخصومات:</strong>
                                <ul class="list-unstyled">
                                    @foreach($salary->deductions as $deduction)
                                        <li><span class="badge badge-danger">{{ $deduction['name'] }}: {{ number_format($deduction['amount'], 2) }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-12">
                            <strong>إجمالي الراتب:</strong> 
                            @php
                                $totalAllowances = is_array($salary->allowances) ? array_sum(array_column($salary->allowances, 'amount')) : 0;
                                $totalDeductions = is_array($salary->deductions) ? array_sum(array_column($salary->deductions, 'amount')) : 0;
                                $netSalary = $salary->basic_salary + $totalAllowances - $totalDeductions;
                            @endphp
                            {{ number_format($netSalary, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($salaryHistory && $salaryHistory->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">سجل الرواتب السابقة</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>الراتب الأساسي</th>
                                        <th>البدلات</th>
                                        <th>الخصومات</th>
                                        <th>تاريخ السريان</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salaryHistory as $historySalary)
                                        <tr>
                                            <td>{{ number_format($historySalary->basic_salary, 2) }}</td>
                                            <td>
                                                @if(is_array($historySalary->allowances) && count($historySalary->allowances))
                                                    @foreach($historySalary->allowances as $a)
                                                        <span class="badge badge-success">{{ $a['name'] }}: {{ number_format($a['amount'], 2) }}</span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(is_array($historySalary->deductions) && count($historySalary->deductions))
                                                    @foreach($historySalary->deductions as $d)
                                                        <span class="badge badge-danger">{{ $d['name'] }}: {{ number_format($d['amount'], 2) }}</span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $historySalary->effective_from }}</td>
                                            <td>{{ $historySalary->effective_to ?? 'غير محدد' }}</td>
                                            <td>
                                                <a href="{{ route('salaries.show', $historySalary->id) }}" class="btn btn-sm btn-info">عرض</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection 