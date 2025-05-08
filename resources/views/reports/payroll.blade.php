@extends('layouts.app')
@section('title', __('messages.payroll_report'))
@section('content')
@if(isset($export) && $export)
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            direction: rtl;
        }
    </style>
@endif
<div class="container">
    <h2 class="mb-4">{{ __('messages.payroll_report') }}</h2>
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ Route::localizedRoute('reports.payroll.excel', request()->only(['month','employee'])) }}" class="btn btn-outline-success me-2"><i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}</a>
        <a href="{{ Route::localizedRoute('reports.payroll.pdf', request()->only(['month','employee'])) }}" class="btn btn-outline-danger me-2"><i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}</a>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print"></i> {{ __('messages.print') }}</button>
    </div>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="month" class="form-label">{{ __('messages.month') }}</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ request('month') }}">
        </div>
        <div class="col-md-4">
            <label for="employee" class="form-label">{{ __('messages.employee') }}</label>
            <input type="text" name="employee" id="employee" class="form-control" placeholder="{{ __('messages.search_employee') }}" value="{{ request('employee') }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">{{ __('messages.show_report') }}</button>
        </div>
    </form>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.employee_name') }}</th>
                            <th>{{ __('messages.month') }}</th>
                            <th>{{ __('messages.basic_salary') }}</th>
                            <th>{{ __('messages.allowances') }}</th>
                            <th>{{ __('messages.deductions') }}</th>
                            <th>{{ __('messages.net_salary') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            <td>{{ $row->employee->name ?? '-' }}</td>
                            <td>{{ $row->salary_month }}</td>
                            <td>{{ number_format($row->gross_salary, 2) }}</td>
                            <td>{{ number_format($row->total_allowances, 2) }}</td>
                            <td>{{ number_format($row->total_deductions, 2) }}</td>
                            <td>{{ number_format($row->net_salary, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>{{ __('messages.total') }}</th>
                            <th></th>
                            <th>{{ number_format($totalGross, 2) }}</th>
                            <th>{{ number_format($totalAllowances, 2) }}</th>
                            <th>{{ number_format($totalDeductions, 2) }}</th>
                            <th>{{ number_format($totalNet, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 