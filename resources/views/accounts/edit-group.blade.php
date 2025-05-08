@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>@lang('messages.edit_category')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>@lang('messages.validation_errors')</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ Route::localizedRoute('accounts.update', ['account' => $account->id, ]) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="is_group" value="1">

                <div class="form-group">
                    <label>@lang('messages.category_name')</label>
                    <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                </div>

                <div class="form-group">
                    <label>@lang('messages.category_code')</label>
                    <input type="text" class="form-control" value="{{ $account->code }}" disabled>
                    <input type="hidden" name="code" value="{{ $account->code }}">
                </div>

                <div class="form-group">
                    <label>@lang('messages.account_type')</label>
                    <select name="type" class="form-control" required>
                        <option value="">@lang('messages.select_option')</option>
                        <option value="asset" {{ $account->type == 'asset' ? 'selected' : '' }}>@lang('messages.type_asset')</option>
                        <option value="liability" {{ $account->type == 'liability' ? 'selected' : '' }}>@lang('messages.type_liability')</option>
                        <option value="revenue" {{ $account->type == 'revenue' ? 'selected' : '' }}>@lang('messages.type_revenue')</option>
                        <option value="expense" {{ $account->type == 'expense' ? 'selected' : '' }}>@lang('messages.type_expense')</option>
                        <option value="equity" {{ $account->type == 'equity' ? 'selected' : '' }}>@lang('messages.type_equity')</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('messages.parent_category_optional')</label>
                    <select name="parent_id" class="form-control">
                        <option value="">@lang('messages.none_option')</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $account->parent_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">@lang('messages.update')</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
            </form>
        </div>
    </section>
</div>
@endsection
