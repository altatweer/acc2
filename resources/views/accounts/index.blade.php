@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.account_list_title')</h1>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary mt-3">@lang('messages.add_account_category')</a>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('messages.account_name')</th>
                                <th>@lang('messages.parent_category_name')</th>
                                <th>@lang('messages.accounting_type')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->parent ? $account->parent->name : '-' }}</td>
                                    <td>
                                        @switch($account->parent->type ?? null)
                                            @case('asset') @lang('messages.account_type_asset') @break
                                            @case('liability') @lang('messages.account_type_liability') @break
                                            @case('revenue') @lang('messages.account_type_revenue') @break
                                            @case('expense') @lang('messages.account_type_expense') @break
                                            @case('equity') @lang('messages.account_type_equity') @break
                                            @default -
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ Route::localizedRoute('accounts.edit', ['account' => $account->id, ]) }}" class="btn btn-info btn-sm">@lang('messages.edit')</a>
                                        <form action="{{ Route::localizedRoute('accounts.destroy', ['account' => $account->id, ]) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('@lang('messages.delete_confirmation_account')')" class="btn btn-danger btn-sm">@lang('messages.delete')</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
