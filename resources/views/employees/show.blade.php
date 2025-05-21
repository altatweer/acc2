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
                                    <span class="badge badge-success">@lang('messages.status_active')</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge badge-warning">@lang('messages.status_inactive')</span>
                                @elseif($employee->status == 'terminated')
                                    <span class="badge badge-danger">@lang('messages.status_terminated')</span>
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