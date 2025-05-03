@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">تعديل بيانات الموظف</h1>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">عودة للقائمة</a>
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
                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>الاسم</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $employee->name) }}">
                        </div>
                        <div class="form-group">
                            <label>الرقم الوظيفي</label>
                            <input type="text" name="employee_number" class="form-control" required value="{{ old('employee_number', $employee->employee_number) }}">
                        </div>
                        <div class="form-group">
                            <label>القسم</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}">
                        </div>
                        <div class="form-group">
                            <label>الوظيفة</label>
                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $employee->job_title) }}">
                        </div>
                        <div class="form-group">
                            <label>تاريخ التعيين</label>
                            <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date ? date('Y-m-d', strtotime($employee->hire_date)) : '') }}">
                        </div>
                        <div class="form-group">
                            <label>الحالة</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $employee->status)=='active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $employee->status)=='inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="terminated" {{ old('status', $employee->status)=='terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>العملة</label>
                            <select name="currency" class="form-control" required>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency', $employee->currency)==$cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">تحديث</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 