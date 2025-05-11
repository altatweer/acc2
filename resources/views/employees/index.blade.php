@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.employees_list')</h1>
            <div class="card-tools">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('add_employee'))
                <a href="{{ route('employees.create') }}" class="btn btn-sm btn-success">@lang('messages.new_employee')</a>
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
                                <th>@lang('messages.employee_name')</th>
                                <th>@lang('messages.employee_number')</th>
                                <th>@lang('messages.department')</th>
                                <th>@lang('messages.job_title')</th>
                                <th>@lang('messages.currency')</th>
                                <th>@lang('messages.status')</th>
                                <th>@lang('messages.actions')</th>
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
                                    <td>
                                        @if($emp->status == 'active')
                                            @lang('messages.status_active')
                                        @elseif($emp->status == 'inactive')
                                            @lang('messages.status_inactive')
                                        @elseif($emp->status == 'terminated')
                                            @lang('messages.status_terminated')
                                        @else
                                            {{ $emp->status }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('view_employees'))
                                            <a href="{{ Route::localizedRoute('employees.show', ['employee' => $emp->id, ]) }}" class="btn btn-outline-info" title="@lang('messages.view')"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('edit_employee'))
                                            <a href="{{ Route::localizedRoute('employees.edit', ['employee' => $emp->id, ]) }}" class="btn btn-outline-primary" title="@lang('messages.edit')"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('delete_employee'))
                                            <form action="{{ Route::localizedRoute('employees.destroy', ['employee' => $emp->id, ]) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_employee_confirm')');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')"><i class="fas fa-trash"></i></button></form>
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