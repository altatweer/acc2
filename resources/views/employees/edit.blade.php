@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.edit_employee')</h1>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ Route::localizedRoute('employees.update', ['employee' => $employee->id, ]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>@lang('messages.employee_name')</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $employee->name) }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.employee_number')</label>
                            <input type="text" name="employee_number" class="form-control" required value="{{ old('employee_number', $employee->employee_number) }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.department')</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.job_title')</label>
                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $employee->job_title) }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.hire_date')</label>
                            <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date ? date('Y-m-d', strtotime($employee->hire_date)) : '') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.status')</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $employee->status)=='active' ? 'selected' : '' }}>@lang('messages.status_active')</option>
                                <option value="inactive" {{ old('status', $employee->status)=='inactive' ? 'selected' : '' }}>@lang('messages.status_inactive')</option>
                                <option value="terminated" {{ old('status', $employee->status)=='terminated' ? 'selected' : '' }}>@lang('messages.status_terminated')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.currency')</label>
                            <select name="currency" class="form-control" required>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency', $employee->currency)==$cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">@lang('messages.update')</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 