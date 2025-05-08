@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.add_new_account_category')</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>@lang('messages.account_category_name')</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.account_category_code')</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('messages.record_type')</label>
                            <select name="is_group" id="is_group" class="form-control" onchange="toggleFields()" required>
                                <option value="1">@lang('messages.group_option')</option>
                                <option value="0">@lang('messages.account_option')</option>
                            </select>
                        </div>

                        <div id="group_fields">
                            <div class="form-group">
                                <label>@lang('messages.main_account_type')</label>
                                <select name="type" class="form-control">
                                    <option value="">@lang('messages.select_option')</option>
                                    <option value="asset">@lang('messages.account_type_asset')</option>
                                    <option value="liability">@lang('messages.account_type_liability')</option>
                                    <option value="revenue">@lang('messages.account_type_revenue')</option>
                                    <option value="expense">@lang('messages.account_type_expense')</option>
                                    <option value="equity">@lang('messages.account_type_equity')</option>
                                </select>
                            </div>
                        </div>

                        <div id="account_fields" style="display:none;">
                            <div class="form-group">
                                <label>@lang('messages.select_parent_category')</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">@lang('messages.select_category')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('messages.account_nature')</label>
                                <select name="nature" class="form-control" required>
                                    <option value="debit">@lang('messages.debit_nature')</option>
                                    <option value="credit">@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
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
