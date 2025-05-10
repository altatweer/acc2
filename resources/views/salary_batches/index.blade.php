@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.salary_batches_list')</h1>
            <a href="{{ route('salary-batches.create') }}" class="btn btn-primary">@lang('messages.new_salary_batch')</a>
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
                                <th>@lang('messages.month')</th>
                                <th>@lang('messages.status')</th>
                                <th>@lang('messages.employees_count')</th>
                                <th>@lang('messages.creation_date')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $batch->id }}</td>
                                    <td>{{ $batch->month }}</td>
                                    <td>
                                        @if($batch->status=='pending')<span class="badge badge-warning">@lang('messages.status_pending')</span>@endif
                                        @if($batch->status=='approved')<span class="badge badge-success">@lang('messages.status_approved')</span>@endif
                                        @if($batch->status=='closed')<span class="badge badge-secondary">@lang('messages.status_closed')</span>@endif
                                    </td>
                                    <td>{{ $batch->salaryPayments()->count() }}</td>
                                    <td>{{ $batch->created_at }}</td>
                                    <td>
                                        <a href="{{ Route::localizedRoute('salary-batches.show', ['salary_batch' => $batch, ]) }}" class="btn btn-sm btn-info">@lang('messages.view')</a>
                                        @if($batch->status=='pending')
                                        <form action="{{ Route::localizedRoute('salary-batches.approve', ['salaryBatch' => $batch->id]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('@lang('messages.approve_batch_confirm')');">
                                            @csrf
                                            <button class="btn btn-sm btn-success">@lang('messages.approve')</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($batches->count() == 0)
                                <tr><td colspan="6" class="text-center">@lang('messages.no_batches_yet')</td></tr>
                            @endif
                        </tbody>
                    </table>
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 