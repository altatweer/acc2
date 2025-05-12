@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">@lang('messages.real_accounts_list')</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('messages.close')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0" id="realAccountsTable">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>@lang('messages.account_code')</th>
                                <th>@lang('messages.account_name')</th>
                                <th>@lang('messages.parent_category')</th>
                                <th>@lang('messages.account_nature')</th>
                                <th>@lang('messages.is_cash_box')</th>
                                <th>@lang('messages.account_currency')</th>
                                <th style="width:120px;">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $i => $account)
                                <tr>
                                    <td>{{ $accounts->firstItem() + $i }}</td>
                                    <td>{{ $account->code }}</td>
                                    <td class="text-left">{{ $account->name }}</td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td>
                                        @if($account->nature == 'debit')
                                            <span class="badge badge-info">@lang('messages.debit_nature')</span>
                                        @elseif($account->nature == 'credit')
                                            <span class="badge badge-warning">@lang('messages.credit_nature')</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($account->is_cash_box)
                                            <span class="badge badge-success">@lang('messages.yes')</span>
                                        @else
                                            <span class="badge badge-secondary">@lang('messages.no')</span>
                                        @endif
                                    </td>
                                    <td>{{ $account->currency ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-outline-info" title="@lang('messages.details')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary" title="@lang('messages.edit')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_confirmation_account')');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4">@lang('messages.no_accounts_to_display')</td>
                                </tr>
                                <tr style="display:none;"><td colspan="8"></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>@lang('messages.total_accounts') <strong>{{ $accounts->total() }}</strong></div>
                <div>{{ $accounts->appends(['locale' => app()->getLocale()])->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    $('#realAccountsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/' + (document.documentElement.lang === 'ar' ? 'ar.json' : 'en.json')
        },
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        responsive: true
    });
});
</script>
@endpush
