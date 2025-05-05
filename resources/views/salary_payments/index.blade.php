@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">دفعات الرواتب</h1>
            <a href="{{ route('salary-payments.create') }}" class="btn btn-primary">إضافة دفعة راتب</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الموظف</th>
                                <th>الشهر</th>
                                <th>الراتب الصافي</th>
                                <th>تاريخ الدفع</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->employee->name ?? '-' }}</td>
                                    <td>{{ $payment->salary_month }}</td>
                                    <td>{{ number_format($payment->net_salary, 2) }}</td>
                                    <td>{{ $payment->payment_date }}</td>
                                    <td>{{ $payment->status }}</td>
                                    <td>
                                        <a href="{{ route('salary-payments.show', $payment) }}" class="btn btn-sm btn-info">عرض</a>
                                        @if($payment->status == 'pending')
                                            <a href="{{ route('salary-payments.create', ['salary_batch_id' => $payment->salary_batch_id, 'employee_id' => $payment->employee_id]) }}" class="btn btn-sm btn-success">دفع</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($payments->count() == 0)
                                <tr><td colspan="7" class="text-center">لا توجد دفعات بعد.</td></tr>
                            @endif
                        </tbody>
                    </table>
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 