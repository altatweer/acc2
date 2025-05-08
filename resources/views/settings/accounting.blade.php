@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">@lang('messages.accounting_settings')</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounting-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('messages.currency')</th>
                                    <th>@lang('messages.default_sales_account')</th>
                                    <th>@lang('messages.default_purchases_account')</th>
                                    <th>@lang('messages.default_customers_account')</th>
                                    <th>@lang('messages.default_suppliers_account')</th>
                                    <th>@lang('messages.salary_expense_account')</th>
                                    <th>@lang('messages.employee_liabilities_account')</th>
                                    <th>@lang('messages.deductions_account')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>{{ $currency->code }}</td>
                                        <td>
                                            <select name="sales_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->sales_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="purchases_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->purchases_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="receivables_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->receivables_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="payables_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->payables_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="expenses_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->expenses_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="liabilities_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->liabilities_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="deductions_account_id[{{ $currency->code }}]" class="form-control">
                                                <option value="">@lang('messages.select')</option>
                                                @foreach($accounts as $acc)
                                                    @if($acc->currency == $currency->code)
                                                        <option value="{{ $acc->id }}" {{ (isset($settings[$currency->code]) && $settings[$currency->code]->deductions_account_id == $acc->id) ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success">@lang('messages.save_settings')</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<script>
$(function(){
    $('select.form-control').select2({
        width: '100%',
        dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
        language: "{{ app()->getLocale() }}",
        placeholder: '@lang('messages.select')',
        allowClear: true
    });
});
</script>
@endpush 