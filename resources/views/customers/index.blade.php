@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">@lang('messages.customers_list')</h3>
                <div class="card-tools">
                    @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                    @if($isSuperAdmin || auth()->user()->can('إضافة عميل'))
                    <a href="{{ route('customers.create') }}" class="btn btn-sm btn-success">@lang('messages.new_customer')</a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('messages.close')"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('messages.customer_name')</th>
                                <th>@lang('messages.customer_email')</th>
                                <th>@lang('messages.customer_phone')</th>
                                <th>@lang('messages.receivables_account')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $i => $cust)
                            <tr>
                                <td>{{ $customers->firstItem() + $i }}</td>
                                <td class="text-left">{{ $cust->name }}</td>
                                <td>{{ $cust->email }}</td>
                                <td>{{ $cust->phone ?? '-' }}</td>
                                <td>{{ $cust->account->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($isSuperAdmin || auth()->user()->can('عرض العملاء'))
                                        <a href="{{ Route::localizedRoute('customers.show', ['customer' => $cust, ]) }}" class="btn btn-outline-info" title="@lang('messages.view')"><i class="fas fa-eye"></i></a>
                                        @endif
                                        @if($isSuperAdmin || auth()->user()->can('تعديل عميل'))
                                        <a href="{{ Route::localizedRoute('customers.edit', ['customer' => $cust, ]) }}" class="btn btn-outline-primary" title="@lang('messages.edit')"><i class="fas fa-edit"></i></a>
                                        @endif
                                        @if($isSuperAdmin || auth()->user()->can('حذف عميل'))
                                        <form action="{{ Route::localizedRoute('customers.destroy', ['customer' => $cust, ]) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_customer_confirm')');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')"><i class="fas fa-trash"></i></button></form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-4">@lang('messages.no_customers_to_display')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>@lang('messages.total_customers') <strong>{{ $customers->total() }}</strong></div>
                <div>{{ $customers->appends(['lang' => app()->getLocale()])->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 