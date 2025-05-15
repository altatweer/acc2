@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.salaries_list')</h1>
            <div class="card-tools">
                @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                @if($isSuperAdmin || auth()->user()->can('add_salary'))
                <a href="{{ route('salaries.create') }}" class="btn btn-sm btn-success">@lang('messages.new_salary')</a>
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
                                <th>@lang('messages.employee')</th>
                                <th>@lang('messages.basic_salary')</th>
                                <th>@lang('messages.allowances')</th>
                                <th>@lang('messages.deductions')</th>
                                <th>@lang('messages.effective_from')</th>
                                <th>@lang('messages.effective_to')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaries as $salary)
                                <tr>
                                    <td>{{ $salary->id }}</td>
                                    <td>{{ $salary->employee->name ?? '-' }}</td>
                                    <td>{{ number_format($salary->basic_salary, 2) }}</td>
                                    <td>
                                        @if(is_array($salary->allowances) && count($salary->allowances))
                                            @foreach($salary->allowances as $a)
                                                <span class="badge badge-success">{{ $a['name'] }}: {{ number_format($a['amount'], 2) }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_array($salary->deductions) && count($salary->deductions))
                                            @foreach($salary->deductions as $d)
                                                <span class="badge badge-danger">{{ $d['name'] }}: {{ number_format($d['amount'], 2) }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $salary->effective_from }}</td>
                                    <td>{{ $salary->effective_to ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($isSuperAdmin || auth()->user()->can('view_salaries'))
                                            <a href="{{ Route::localizedRoute('salaries.show', ['salary' => $salary, ]) }}" class="btn btn-outline-info" title="@lang('messages.view')"><i class="fas fa-eye"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('edit_salary'))
                                            <a href="{{ Route::localizedRoute('salaries.edit', ['salary' => $salary, ]) }}" class="btn btn-outline-primary" title="@lang('messages.edit')"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if($isSuperAdmin || auth()->user()->can('delete_salary'))
                                            <form action="{{ Route::localizedRoute('salaries.destroy', ['salary' => $salary, ]) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_salary_confirm')');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')"><i class="fas fa-trash"></i></button></form>
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