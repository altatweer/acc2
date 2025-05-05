@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة الرواتب</h1>
            <div class="card-tools">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('إضافة راتب'))
                <a href="{{ route('salaries.create') }}" class="btn btn-sm btn-success">إضافة راتب</a>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('عرض الرواتب'))
                                            <a href="{{ route('salaries.show', $salary) }}" class="btn btn-outline-info" title="عرض"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('تعديل راتب'))
                                            <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-outline-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('حذف راتب'))
                                            <form action="{{ route('salaries.destroy', $salary) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="حذف"><i class="fas fa-trash"></i></button></form>
                                            @endif
                                        </div>
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