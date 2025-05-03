@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">تفاصيل الموظف</h1>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">عودة للقائمة</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>الاسم</th>
                            <td>{{ $employee->name }}</td>
                        </tr>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <td>{{ $employee->employee_number }}</td>
                        </tr>
                        <tr>
                            <th>القسم</th>
                            <td>{{ $employee->department }}</td>
                        </tr>
                        <tr>
                            <th>الوظيفة</th>
                            <td>{{ $employee->job_title }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ التعيين</th>
                            <td>{{ $employee->hire_date }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>{{ $employee->status }}</td>
                        </tr>
                        <tr>
                            <th>العملة</th>
                            <td>{{ $employee->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 