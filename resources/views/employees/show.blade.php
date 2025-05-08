@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.view_employee')</h1>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('messages.employee_name')</th>
                            <td>{{ $employee->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.employee_number')</th>
                            <td>{{ $employee->employee_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.department')</th>
                            <td>{{ $employee->department }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.job_title')</th>
                            <td>{{ $employee->job_title }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.hire_date')</th>
                            <td>{{ $employee->hire_date }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.status')</th>
                            <td>
                                @if($employee->status == 'active')
                                    @lang('messages.status_active')
                                @elseif($employee->status == 'inactive')
                                    @lang('messages.status_inactive')
                                @elseif($employee->status == 'terminated')
                                    @lang('messages.status_terminated')
                                @else
                                    {{ $employee->status }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('messages.currency')</th>
                            <td>{{ $employee->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 