@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.edit_account_category')</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounts.update', ['account' => $account->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>@lang('messages.account_category_name')</label>
                            <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.account_category_code')</label>
                            <input type="text" name="code" value="{{ $account->code }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('messages.record_type')</label>
                            <select name="is_group" id="is_group" class="form-control" onchange="toggleFields()" required>
                                <option value="1" {{ $account->is_group ? 'selected' : '' }}>@lang('messages.group_option')</option>
                                <option value="0" {{ !$account->is_group ? 'selected' : '' }}>@lang('messages.account_option')</option>
                            </select>
                        </div>

                        <div id="group_fields">
                            <div class="form-group">
                                <label>@lang('messages.main_account_type')</label>
                                <select name="type" class="form-control">
                                    <option value="">@lang('messages.select_option')</option>
                                    <option value="asset" {{ $account->type == 'asset' ? 'selected' : '' }}>@lang('messages.account_type_asset')</option>
                                    <option value="liability" {{ $account->type == 'liability' ? 'selected' : '' }}>@lang('messages.account_type_liability')</option>
                                    <option value="revenue" {{ $account->type == 'revenue' ? 'selected' : '' }}>@lang('messages.account_type_revenue')</option>
                                    <option value="expense" {{ $account->type == 'expense' ? 'selected' : '' }}>@lang('messages.account_type_expense')</option>
                                    <option value="equity" {{ $account->type == 'equity' ? 'selected' : '' }}>@lang('messages.account_type_equity')</option>
                                </select>
                            </div>
                        </div>

                        <div id="account_fields">
                            <div class="form-group">
                                <label>@lang('messages.select_parent_category')</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">@lang('messages.select_category')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $account->parent_id == $category->id ? 'selected' : '' }}
                                                data-currency="{{ $category->currency ?? '' }}"
                                                style="color: {{ ($category->currency ?? '') == 'IQD' ? '#1976d2' : (($category->currency ?? '') == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                            {{ $category->name }}
                                            @if($category->currency)
                                                <span style="font-weight: bold; background: {{ ($category->currency ?? '') == 'IQD' ? '#e3f2fd' : (($category->currency ?? '') == 'USD' ? '#e8f5e8' : '#f3e5f5') }}; padding: 2px 6px; border-radius: 3px; font-size: 0.85em;">
                                                    {{ strtoupper($category->currency) }}
                                                </span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('messages.account_nature')</label>
                                <select name="nature" class="form-control">
                                    <option value="">@lang('messages.select_option')</option>
                                    <option value="debit" {{ $account->nature == 'debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                    <option value="credit" {{ $account->nature == 'credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">@lang('messages.save_changes')</button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">@lang('messages.back')</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleFields() {
    var isGroup = document.getElementById('is_group').value;
    if (isGroup == "1") {
        document.getElementById('group_fields').style.display = 'block';
        document.getElementById('account_fields').style.display = 'none';
    } else {
        document.getElementById('group_fields').style.display = 'none';
        document.getElementById('account_fields').style.display = 'block';
    }
}
window.onload = toggleFields;
</script>

@endsection
