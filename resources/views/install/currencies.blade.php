@extends('layouts.install')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark text-center">
                    <h2>Select Main Currencies</h2>
                    <p class="mb-0">Choose the currencies your system will use (you can select more than one).<br><span class="text-danger">At least one currency is required. USD is selected by default.</span></p>
                </div>
                <div class="card-body">
                    @if(session('currencies_error'))
                        <div class="alert alert-danger text-center">{{ session('currencies_error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('install.saveCurrencies') }}">
                        @csrf
                        <div class="form-group">
                            <label>Available Currencies:</label>
                            <div class="row">
                                @php
                                    $currencies = [
                                        ['code' => 'USD', 'name' => 'US Dollar'],
                                        ['code' => 'EUR', 'name' => 'Euro'],
                                        ['code' => 'SAR', 'name' => 'Saudi Riyal'],
                                        ['code' => 'AED', 'name' => 'UAE Dirham'],
                                        ['code' => 'EGP', 'name' => 'Egyptian Pound'],
                                        ['code' => 'JOD', 'name' => 'Jordanian Dinar'],
                                        ['code' => 'KWD', 'name' => 'Kuwaiti Dinar'],
                                        ['code' => 'QAR', 'name' => 'Qatari Riyal'],
                                        ['code' => 'OMR', 'name' => 'Omani Riyal'],
                                        ['code' => 'BHD', 'name' => 'Bahraini Dinar'],
                                        ['code' => 'IQD', 'name' => 'Iraqi Dinar'],
                                        ['code' => 'TRY', 'name' => 'Turkish Lira'],
                                        ['code' => 'GBP', 'name' => 'British Pound'],
                                    ];
                                    $oldCurrencies = old('currencies', ['USD']);
                                @endphp
                                @foreach($currencies as $currency)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input currency-checkbox" type="checkbox" name="currencies[]" id="currency_{{ $currency['code'] }}" value="{{ $currency['code'] }}" {{ is_array($oldCurrencies) && in_array($currency['code'], $oldCurrencies) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="currency_{{ $currency['code'] }}">
                                                {{ $currency['name'] }} ({{ $currency['code'] }})
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('currencies')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" id="default-currency-group" style="display:none;">
                            <label for="default_currency">Select Default Currency <span class="text-danger">*</span></label>
                            <select name="default_currency" id="default_currency" class="form-control">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency['code'] }}" {{ (old('default_currency', 'USD') == $currency['code']) ? 'selected' : '' }}>{{ $currency['name'] }} ({{ $currency['code'] }})</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">You can change the default currency later from the control panel.</small>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg btn-block">Save Currencies & Continue <i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function updateDefaultCurrencyVisibility() {
        var checked = document.querySelectorAll('.currency-checkbox:checked').length;
        var group = document.getElementById('default-currency-group');
        if (checked > 1) {
            group.style.display = '';
        } else {
            group.style.display = 'none';
        }
    }
    document.querySelectorAll('.currency-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateDefaultCurrencyVisibility);
    });
    window.onload = updateDefaultCurrencyVisibility;
</script>
@endsection 