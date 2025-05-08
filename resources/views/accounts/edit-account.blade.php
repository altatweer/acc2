@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">@lang('messages.edit_real_account')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning">
                <div class="card-header">
                    <h5 class="card-title">@lang('messages.real_account_data')</h5>
                </div>
                <form action="{{ Route::localizedRoute('accounts.update', ['account' => $account, ]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="is_group" value="0">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">@lang('messages.account_name')</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('messages.account_code')</label>
                                <input type="text" id="code" class="form-control" value="{{ $account->code }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $account->code }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">@lang('messages.select_parent_category')</label>
                                <select id="parent_id" name="parent_id" class="form-control" required>
                                    <option value="">-- @lang('messages.select_category') --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id', $account->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">@lang('messages.account_nature')</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- @lang('messages.select_option') --</option>
                                    <option value="debit" {{ old('nature', $account->nature) == 'debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                    <option value="credit" {{ old('nature', $account->nature) == 'credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" id="isCashBox" name="is_cash_box" value="1" class="form-check-input" {{ old('is_cash_box', $account->is_cash_box) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isCashBox">@lang('messages.is_cash_box')</label>
                        </div>

                        <div class="form-group">
                            <label for="currency">@lang('messages.account_currency')</label>
                            <select name="currency" id="currency" class="form-control" required>
                                <option value="" disabled {{ old('currency', $account->currency) ? '' : 'selected' }}>-- @lang('messages.select_currency') --</option>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency', $account->currency) == $cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> @lang('messages.update')
                        </button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> @lang('messages.cancel')
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function refreshAccountCode(){
        let isGroup = 0;
        let parent_id = $('#parent_id').val() || '';
        $.getJSON("{{ route('accounts.nextCode') }}", { is_group: isGroup, parent_id: parent_id }, function(data){
            $('#code').val(data.nextCode);
            $('#codeInput').val(data.nextCode);
        });
    }
    $('#parent_id').on('change', refreshAccountCode);
});
</script>
@endpush
