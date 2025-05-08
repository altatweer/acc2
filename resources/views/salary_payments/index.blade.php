@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.salary_payments_list')</h1>
            <a href="{{ route('salary-payments.create') }}" class="btn btn-primary">@lang('messages.add_salary_payment')</a>
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
                                <th>@lang('messages.employee')</th>
                                <th>@lang('messages.salary_month')</th>
                                <th>@lang('messages.net_salary')</th>
                                <th>@lang('messages.payment_date')</th>
                                <th>@lang('messages.status')</th>
                                <th>@lang('messages.actions')</th>
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
                                    <td>
                                        @if($payment->status == 'pending')
                                            @lang('messages.status_pending')
                                        @elseif($payment->status == 'paid')
                                            @lang('messages.status_paid')
                                        @else
                                            {{ $payment->status }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ Route::localizedRoute('salary-payments.show', ['salary_payment' => $payment, ]) }}" class="btn btn-sm btn-info">@lang('messages.view')</a>
                                        @if($payment->status == 'pending')
                                            <a href="{{ Route::localizedRoute('salary-payments.create', ['salary_batch_id' => $payment->salary_batch_id, 'employee_id' => $payment->employee_id, ]) }}" class="btn btn-sm btn-success">@lang('messages.pay')</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($payments->count() == 0)
                                <tr><td colspan="7" class="text-center">@lang('messages.no_payments_yet')</td></tr>
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