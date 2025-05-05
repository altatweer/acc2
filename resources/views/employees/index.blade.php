@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة الموظفين</h1>
            <div class="card-tools">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('إضافة موظف'))
                <a href="{{ route('employees.create') }}" class="btn btn-sm btn-success">موظف جديد</a>
                @endif
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
            </div>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('عرض الموظفين'))
                                            <a href="{{ route('employees.show', $emp->id) }}" class="btn btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('تعديل موظف'))
                                            <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('حذف موظف'))
                                            <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button></form>
                                            @endif
                                        </div>
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