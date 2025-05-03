@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة الرواتب</h1>
            <a href="{{ route('salaries.create') }}" class="btn btn-primary">إضافة راتب جديد</a>
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
                                <th>الراتب الأساسي</th>
                                <th>البدلات</th>
                                <th>الخصومات</th>
                                <th>من تاريخ</th>
                                <th>إلى تاريخ</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaries as $salary)
                                <tr>
                                    <td>{{ $salary->id }}</td>
                                    <td>{{ $salary->employee->name ?? '-' }}</td>
                                    <td>{{ number_format($salary->basic_salary, 2) }}</td>
                                    <td>
                                        @if($salary->allowances)
                                            @foreach($salary->allowances as $a)
                                                <span class="badge badge-success">{{ $a['name'] }}: {{ number_format($a['amount'], 2) }}</span>
                                            @endforeach
                                        @else - @endif
                                    </td>
                                    <td>
                                        @if($salary->deductions)
                                            @foreach($salary->deductions as $d)
                                                <span class="badge badge-danger">{{ $d['name'] }}: {{ number_format($d['amount'], 2) }}</span>
                                            @endforeach
                                        @else - @endif
                                    </td>
                                    <td>{{ $salary->effective_from }}</td>
                                    <td>{{ $salary->effective_to ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('salaries.show', $salary->id) }}" class="btn btn-info btn-sm">عرض</a>
                                        <a href="{{ route('salaries.edit', $salary->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                        <form action="{{ route('salaries.destroy', $salary->id) }}" method="POST" style="display:inline-block;">
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
                        {{ $salaries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 