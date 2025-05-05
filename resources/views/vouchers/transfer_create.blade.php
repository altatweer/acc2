@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>إضافة سند تحويل بين الصناديق</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('vouchers.index', ['type'=>'transfer']) }}" class="btn btn-secondary">رجوع</a>
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
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('vouchers.transfer.store') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>الصندوق المحول منه</label>
                            <select name="account_id" class="form-control" required id="from-account">
                                <option value="">اختر الصندوق</option>
                                @foreach($cashAccounts as $acc)
                                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency ?? '' }}">
                                        {{ $acc->name }} @if($acc->currency) ({{ $acc->currency }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>الصندوق المحول إليه</label>
                            <select name="target_account_id" class="form-control" required id="to-account">
                                <option value="">اختر الصندوق</option>
                                @foreach($cashAccounts as $acc)
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
                            <label>التاريخ</label>
                            <input type="datetime-local" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-4" id="amount_from_group">
                            <label id="amount_from_label">المبلغ المحول</label>
                            <input type="number" name="amount" id="amount_from" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group col-md-4" id="exchange_rate_group" style="display:none;">
                            <label>سعر الصرف</label>
                            <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-4" id="amount_to_group" style="display:none;">
                            <label id="amount_to_label">المبلغ المستلم</label>
                            <input type="number" id="amount_to" class="form-control" readonly>
                        </div>
                    </div>
                    <div id="same-currency-alert" class="alert alert-warning mt-2" style="display:none;">لا يمكن التحويل إلى نفس الصندوق.</div>
                    <div id="no-cashbox-alert" class="alert alert-warning mt-2" style="display:none;"></div>
                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-success" id="save-btn">حفظ سند التحويل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
const cashAccounts = @json($cashAccounts->map(function($a){return ['id'=>$a->id,'currency'=>$a->currency,'name'=>$a->name];}));
const exchangeRates = {
    'IQD_USD': 1300,
    'USD_IQD': 1/1300,
    // أضف أسعار الصرف حسب النظام
};
function filterTargetAccounts() {
    const fromSelect = document.getElementById('from-account');
    const toSelect = document.getElementById('to-account');
    const fromVal = fromSelect.value;
    const fromCurrency = cashAccounts.find(a => a.id == fromVal)?.currency;
    let hasMatch = false;
    Array.from(toSelect.options).forEach(opt => {
        if (!opt.value) return;
        const acc = cashAccounts.find(a => a.id == opt.value);
        // إظهار كل الصناديق ما عدا نفس الصندوق
        if (opt.value === fromVal) {
            opt.disabled = true;
            opt.style.display = 'none';
        } else {
            opt.disabled = false;
            opt.style.display = '';
            hasMatch = true;
        }
    });
    // تحديث واجهة التحويل
    const toVal = toSelect.value;
    const toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
    const rateInput = document.getElementById('exchange_rate');
    const rateGroup = document.getElementById('exchange_rate_group');
    const amountToGroup = document.getElementById('amount_to_group');
    const saveBtn = document.getElementById('save-btn');
    const sameAlert = document.getElementById('same-currency-alert');
    const noBoxAlert = document.getElementById('no-cashbox-alert');
    if (fromVal && toVal && fromVal === toVal) {
        saveBtn.disabled = true;
        sameAlert.style.display = 'block';
    } else {
        saveBtn.disabled = false;
        sameAlert.style.display = 'none';
    }
    // إذا العملة مختلفة: أظهر سعر الصرف والمبلغ المستلم
    if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
        let key = fromCurrency + '_' + toCurrency;
        rateInput.value = exchangeRates[key] || 1;
        rateInput.readOnly = true;
        rateGroup.style.display = '';
        amountToGroup.style.display = '';
    } else {
        rateInput.value = '';
        rateGroup.style.display = 'none';
        amountToGroup.style.display = 'none';
    }
    // رسالة إذا لا يوجد صناديق أخرى
    if (!hasMatch && fromVal) {
        noBoxAlert.style.display = 'block';
        noBoxAlert.innerText = 'لا يوجد صناديق أخرى للتحويل إليها.';
    } else {
        noBoxAlert.style.display = 'none';
    }
}
function updateAmountTo() {
    const fromSelect = document.getElementById('from-account');
    const toSelect = document.getElementById('to-account');
    const fromVal = fromSelect.value;
    const toVal = toSelect.value;
    const fromCurrency = cashAccounts.find(a => a.id == fromVal)?.currency;
    const toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
    const amountFrom = parseFloat(document.getElementById('amount_from').value) || 0;
    const rate = parseFloat(document.getElementById('exchange_rate').value) || 1;
    let amountTo = '';
    if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
        let key = fromCurrency + '_' + toCurrency;
        if (exchangeRates[key]) {
            amountTo = amountFrom / exchangeRates[key];
        } else {
            amountTo = amountFrom;
        }
    }
    document.getElementById('amount_to').value = amountTo ? amountTo.toFixed(2) : '';
}
document.getElementById('from-account').addEventListener('change', function(){ filterTargetAccounts(); updateAmountTo(); });
document.getElementById('to-account').addEventListener('change', function(){ filterTargetAccounts(); updateAmountTo(); });
document.getElementById('amount_from').addEventListener('input', updateAmountTo);
document.addEventListener('DOMContentLoaded', function(){ filterTargetAccounts(); updateAmountTo(); });
</script>
@endsection 