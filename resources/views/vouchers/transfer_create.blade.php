@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>@lang('messages.add_transfer_voucher_between_accounts')</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'transfer', ]) }}" class="btn btn-secondary">@lang('messages.back')</a>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>@lang('messages.validation_errors')</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ Route::localizedRoute('vouchers.transfer.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>@lang('messages.source_account')</label>
                            <select name="account_id" class="form-control" required id="from-account">
                                <option value="">@lang('messages.choose_account')</option>
                                @foreach($cashAccountsFrom as $acc)
                                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency ?? '' }}">
                                        {{ $acc->name }} @if($acc->currency) ({{ $acc->currency }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('messages.target_account')</label>
                            <select name="target_account_id" class="form-control" required id="to-account">
                                <option value="">@lang('messages.choose_account')</option>
                                @foreach($cashAccountsTo as $acc)
                                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency ?? '' }}">
                                        {{ $acc->name }} @if($acc->currency) ({{ $acc->currency }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('target_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>@lang('messages.voucher_date')</label>
                            <input type="datetime-local" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-4" id="amount_from_group">
                            <label id="amount_from_label">@lang('messages.transferred_amount')</label>
                            <input type="number" name="amount" id="amount_from" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group col-md-4" id="exchange_rate_group" style="display:none;">
                            <label>@lang('messages.exchange_rate')</label>
                            <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-4" id="amount_to_group" style="display:none;">
                            <label id="amount_to_label">@lang('messages.received_amount')</label>
                            <input type="number" id="amount_to" class="form-control" readonly>
                        </div>
                    </div>
                    <div id="same-currency-alert" class="alert alert-warning mt-2" style="display:none;">@lang('messages.same_account_alert')</div>
                    <div id="no-cashbox-alert" class="alert alert-warning mt-2" style="display:none;"></div>
                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-success" id="save-btn">@lang('messages.save_transfer_voucher')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<!-- تحميل jQuery أولاً -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- تحميل select2 بعد jQuery -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<!-- كود التحويل بين العملات -->
<script>
@php
    $allCashAccounts = $cashAccountsFrom->merge($cashAccountsTo)->unique('id');
@endphp
$(function(){
    const cashAccounts = @json($allCashAccounts->map(function($a){return ['id' => $a->id, 'currency' => $a->currency, 'name' => $a->name];}));
    const exchangeRates = @json($exchangeRates);
    function filterTargetAccounts() {
        console.log('filterTargetAccounts', cashAccounts);
        const fromVal = $('#from-account').val();
        const toVal = $('#to-account').val();
        const fromCurrency = cashAccounts.find(a => a.id == fromVal)?.currency;
        let toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
        if (!fromCurrency || !toCurrency) {
            alert('@lang('messages.currency_alert')');
        }
        let hasMatch = false;
        $('#to-account option').each(function(){
            if (!$(this).val()) return;
            if ($(this).val() === fromVal) {
                $(this).prop('disabled', true).hide();
            } else {
                $(this).prop('disabled', false).show();
                hasMatch = true;
            }
        });
        // تحديث واجهة التحويل
        toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
        const rateInput = $('#exchange_rate');
        const rateGroup = $('#exchange_rate_group');
        const amountToGroup = $('#amount_to_group');
        const saveBtn = $('#save-btn');
        const sameAlert = $('#same-currency-alert');
        const noBoxAlert = $('#no-cashbox-alert');
        if (fromVal && toVal && fromVal === toVal) {
            saveBtn.prop('disabled', true);
            sameAlert.show();
        } else {
            saveBtn.prop('disabled', false);
            sameAlert.hide();
        }
        // إذا العملة مختلفة: أظهر سعر الصرف والمبلغ المستلم
        if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
            let key = fromCurrency + '_' + toCurrency;
            if (exchangeRates[key]) {
                rateInput.val(exchangeRates[key]);
            } else {
                rateInput.val(1);
            }
            rateInput.prop('readonly', true);
            rateGroup.show();
            amountToGroup.show();
            // رسالة تنبيه إذا لم يوجد سعر صرف تلقائي
            let rateAlert = $('#exchange-rate-alert');
            if (!rateAlert.length) {
                rateAlert = $('<div id="exchange-rate-alert" class="alert alert-warning mt-2"></div>');
                rateInput.parent().append(rateAlert);
            }
            if (!exchangeRates[key]) {
                rateAlert.text('@lang('messages.exchange_rate_missing_alert')').show();
            } else {
                rateAlert.hide();
            }
        } else {
            rateInput.val('');
            rateGroup.hide();
            amountToGroup.hide();
            $('#exchange-rate-alert').hide();
        }
        // رسالة إذا لا يوجد صناديق أخرى
        if (!hasMatch && fromVal) {
            noBoxAlert.show().text('@lang('messages.no_other_accounts_alert')');
        } else {
            noBoxAlert.hide();
        }
    }
    function updateAmountTo() {
        const fromVal = $('#from-account').val();
        const toVal = $('#to-account').val();
        const fromCurrency = cashAccounts.find(a => a.id == fromVal)?.currency;
        let toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
        const amountFrom = parseFloat($('#amount_from').val()) || 0;
        const rate = parseFloat($('#exchange_rate').val()) || 1;
        let amountTo = '';
        if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
            let key = fromCurrency + '_' + toCurrency;
            if (exchangeRates[key]) {
                amountTo = amountFrom / exchangeRates[key];
            } else {
                amountTo = amountFrom;
            }
        }
        $('#amount_to').val(amountTo ? amountTo.toFixed(2) : '');
    }
    // تهيئة select2
    $('#from-account, #to-account').select2({
        width: '100%',
        dir: 'rtl',
        language: 'ar',
        placeholder: '@lang('messages.choose_account')',
        allowClear: true
    });
    // ربط الأحداث بعد تهيئة select2
    $('#from-account, #to-account').on('change', function(){ filterTargetAccounts(); updateAmountTo(); });
    $('#amount_from').on('input', updateAmountTo);
    // تنفيذ أولي
    filterTargetAccounts();
    updateAmountTo();
});
</script>
@endpush 