@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة الموظفين</h1>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">إضافة موظف جديد</a>
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
                                <th>الاسم</th>
                                <th>الرقم الوظيفي</th>
                                <th>القسم</th>
                                <th>الوظيفة</th>
                                <th>العملة</th>
                                <th>الحالة</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                                <tr>
                                    <td>{{ $emp->id }}</td>
                                    <td>{{ $emp->name }}</td>
                                    <td>{{ $emp->employee_number }}</td>
                                    <td>{{ $emp->department }}</td>
                                    <td>{{ $emp->job_title }}</td>
                                    <td>{{ $emp->currency }}</td>
                                    <td>{{ $emp->status }}</td>
                                    <td>
                                        <a href="{{ route('employees.show', $emp->id) }}" class="btn btn-info btn-sm">عرض</a>
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 