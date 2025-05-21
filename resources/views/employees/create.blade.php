@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.create_employee')</h1>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">@lang('messages.back_to_list')</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">@lang('messages.employee_details')</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('employees.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>@lang('messages.employee_name')</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.employee_number')</label>
                            <input type="text" name="employee_number" class="form-control" required value="{{ old('employee_number') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.department')</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.job_title')</label>
                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.hire_date')</label>
                            <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.status')</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>@lang('messages.status_active')</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>@lang('messages.status_inactive')</option>
                                <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>@lang('messages.status_terminated')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.currency')</label>
                            <select name="currency" class="form-control" required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" {{ old('currency') == $currency->code ? 'selected' : '' }}>{{ $currency->code }} - {{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 