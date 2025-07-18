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
                            <select name="account_id" class="form-control account-select" required id="from-account">
                                <option value="">@lang('messages.choose_account')</option>
                                @foreach($cashAccountsFrom as $acc)
                                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency ?? '' }}" data-code="{{ $acc->code ?? '' }}">
                                        {{ $acc->code ? $acc->code.' - ' : '' }}{{ $acc->name }} @if($acc->currency) ({{ $acc->currency }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label>@lang('messages.target_account')</label>
                            <select name="target_account_id" class="form-control account-select" required id="to-account">
                                <option value="">@lang('messages.choose_account')</option>
                                @foreach($cashAccountsTo as $acc)
                                    <option value="{{ $acc->id }}" data-currency="{{ $acc->currency ?? '' }}" data-code="{{ $acc->code ?? '' }}">
                                        {{ $acc->code ? $acc->code.' - ' : '' }}{{ $acc->name }} @if($acc->currency) ({{ $acc->currency }}) @endif
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
                            <input type="number" name="exchange_rate" id="exchange_rate" class="form-control" step="0.0000000001" readonly>
                        </div>
                        <div class="form-group col-md-4" id="amount_to_group" style="display:none;">
                            <label id="amount_to_label">@lang('messages.received_amount')</label>
                            <input type="number" id="amount_to" class="form-control" readonly>
                        </div>
                    </div>
                    <div id="same-currency-alert" class="alert alert-warning mt-2" style="display:none;">@lang('messages.same_account_alert')</div>
                    <div id="no-cashbox-alert" class="alert alert-warning mt-2" style="display:none;"></div>
                    <div id="exchange-rate-info" class="alert alert-info mt-2" style="display:none;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>معلومات معدل الصرف:</strong> <span id="exchange-rate-details"></span>
                    </div>
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

<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #007bff;
}
.select2-results__option {
    padding: 8px 12px;
    border-bottom: 1px solid #f2f2f2;
}
.select2-search--dropdown .select2-search__field {
    padding: 8px;
    font-size: 16px;
    border-radius: 4px;
    margin-bottom: 8px;
}
.select2-search--dropdown .select2-search__field:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.select2-container .select2-selection {
    line-height: 24px;
}
.select2-dropdown {
    z-index: 9999;
}
.select2-search--dropdown {
    padding: 10px;
}
.badge {
    font-size: 85%;
    font-weight: 600;
    padding: 4px 8px;
    margin-left: 5px;
}
</style>

<!-- كود التحويل بين العملات -->
<script>
$(document).ready(function(){
    // Obtener los balances de los cashboxes
    let cashAccounts = @json($cashAccountsFrom->concat($cashAccountsTo));
    let exchangeRates = @json($exchangeRates);
    
    console.log('Initial cashAccounts data:', cashAccounts);
    
    // Agregar información de saldo a los cashAccounts (ya están cargados del servidor)
    cashAccounts = cashAccounts.map(account => {
        // El balance ya viene del servidor, pero añadimos validación
        if (account.balance === undefined || account.balance === null) {
            account.balance = 0;
            console.warn('Account without balance loaded:', account);
        }
        return account;
    });
    
    console.log('Processed cashAccounts:', cashAccounts);
    
    // Función para cargar el saldo de la cuenta seleccionada (فقط للحسابات غير المحملة)
    function loadAccountBalance(accountId) {
        if (!accountId) return;
        
        const account = cashAccounts.find(a => a.id == accountId);
        
        // إذا كان الرصيد محمل مسبقاً، لا نحتاج لطلب AJAX
        if (account && account.balance !== undefined && account.balance !== null) {
            console.log('Balance already loaded from server:', account.balance);
            if ($('#from-account').val() == accountId) {
                updateBalanceDisplay();
                validateAmount();
            }
            return;
        }
        
        const currency = account ? account.currency : null;
        
        console.log('Loading balance for account via AJAX:', {
            accountId: accountId,
            currency: currency,
            account: account
        });
        
        $.ajax({
            url: '/api/accounts/' + accountId + '/balance',
            method: 'GET',
            data: { currency: currency },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Balance response:', response);
                const account = cashAccounts.find(a => a.id == accountId);
                if (account) {
                    account.balance = response.balance;
                    // Actualizar la UI si es necesario
                    if ($('#from-account').val() == accountId) {
                        updateBalanceDisplay();
                        validateAmount();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('خطأ في جلب رصيد الحساب:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    accountId: accountId,
                    currency: currency
                });
                
                // عرض رسالة خطأ للمستخدم
                $('#balance-error-alert').remove();
                const errorAlert = $('<div id="balance-error-alert" class="alert alert-warning mt-2">' + 
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    'خطأ في تحميل رصيد الحساب. يرجى المحاولة مرة أخرى.' + 
                    '</div>');
                $('#amount_from_group').after(errorAlert);
            }
        });
    }
    
    // Mostrar el saldo actual de la cuenta origen
    function updateBalanceDisplay() {
        const fromVal = $('#from-account').val();
        const account = cashAccounts.find(a => a.id == fromVal);
        
        if (account) {
            // Eliminar mensaje anterior si existe
            $('#current-balance-info').remove();
            $('#balance-error-alert').remove();
            
            // إذا كان الرصيد محمل من الـ server، استخدمه مباشرة
            if (account.balance !== undefined && account.balance !== null) {
                const balanceInfo = $('<div id="current-balance-info" class="alert alert-info mt-2">' + 
                    '<i class="fas fa-info-circle"></i> ' +
                    'الرصيد الحالي: <strong>' + account.balance.toFixed(2) + ' ' + account.currency + '</strong>' +
                    '</div>');
                $('#amount_from_group').after(balanceInfo);
            } else {
                // إذا لم يكن الرصيد محمل، حمله عبر AJAX
                loadAccountBalance(fromVal);
            }
        }
    }
    
    // Validar si el monto excede el saldo disponible
    function validateAmount() {
        const fromVal = $('#from-account').val();
        const account = cashAccounts.find(a => a.id == fromVal);
        const amount = parseFloat($('#amount_from').val()) || 0;
        
        if (account && account.balance !== undefined && account.balance !== null && amount > account.balance) {
            // Mostrar alerta
            $('#insufficient-balance-alert').remove();
            const alert = $('<div id="insufficient-balance-alert" class="alert alert-danger mt-2">' + 
                '<i class="fas fa-exclamation-triangle"></i> ' +
                '<strong>تنبيه:</strong> المبلغ المطلوب تحويله (' + amount.toFixed(2) + ' ' + account.currency + ') ' +
                'يتجاوز الرصيد المتاح في الصندوق (' + account.balance.toFixed(2) + ' ' + account.currency + ').' + 
                '</div>');
            $('#amount_from_group').after(alert);
            
            // Deshabilitar botón de guardar
            $('#save-btn').prop('disabled', true);
        } else {
            // Eliminar alerta si existe
            $('#insufficient-balance-alert').remove();
            
            // Habilitar botón de guardar si no hay otros problemas
            if ($('#from-account').val() && $('#to-account').val() && 
                $('#from-account').val() !== $('#to-account').val() && amount > 0) {
                $('#save-btn').prop('disabled', false);
            }
        }
    }

    function filterTargetAccounts() {
        const fromVal = $('#from-account').val();
        const toVal = $('#to-account').val();
        const fromCurrency = cashAccounts.find(a => a.id == fromVal)?.currency;
        let toCurrency = cashAccounts.find(a => a.id == toVal)?.currency;
        if (!fromCurrency || !toCurrency) {
            // Silenciosamente continuar
        }
        let hasMatch = false;
        
        // Destroy and rebuild to-account Select2
        if ($('#to-account').hasClass('select2-hidden-accessible')) {
            $('#to-account').select2('destroy');
        }
        
        $('#to-account option').each(function(){
            if (!$(this).val()) return; // Skip placeholder
            if ($(this).val() === fromVal) {
                $(this).prop('disabled', true).hide();
            } else {
                $(this).prop('disabled', false).show();
                hasMatch = true;
            }
        });
        
        // Reinitialize select2 with updated options
        initializeAccountSelect('#to-account');
        
        // If the previously selected "to" account is now disabled (same as "from"), clear the selection
        if (toVal === fromVal) {
            $('#to-account').val('').trigger('change');
            toVal = '';
        }
        
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
        
        // Si hay una cuenta de origen seleccionada, عرض الرصيد بدلاً من تحميله عبر AJAX
        if (fromVal) {
            updateBalanceDisplay();
        }
        
        // إذا العملة مختلفة: أظهر سعر الصرف والمبلغ المستلم
        if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
            let key = fromCurrency + '_' + toCurrency;
            if (exchangeRates[key]) {
                const rate = exchangeRates[key];
                rateInput.val(rate.toFixed(10)); // زيادة الدقة إلى 10 أرقام عشرية
                rateGroup.show();
                amountToGroup.show();
                
                // عرض معلومات معدل الصرف
                $('#exchange-rate-details').text(`1 ${fromCurrency} = ${rate.toFixed(10)} ${toCurrency}`);
                $('#exchange-rate-info').show();
            } else {
                rateGroup.hide();
                amountToGroup.hide();
                $('#exchange-rate-info').hide();
            }
        } else {
            // نفس العملة: أخفِ سعر الصرف
            rateGroup.hide();
            amountToGroup.hide();
            $('#exchange-rate-info').hide();
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
                // استخدام دقة أعلى في الحساب
                amountTo = amountFrom * parseFloat(exchangeRates[key]);
            } else {
                amountTo = amountFrom;
            }
        }
        $('#amount_to').val(amountTo ? amountTo.toFixed(6) : ''); // زيادة الدقة لعرض أفضل
        
        // Validar monto
        validateAmount();
    }
    
    // Function to format account options 
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        // Verificar si hay datos de moneda
        const currency = $(account.element).data('currency');
        const code = $(account.element).data('code');
        
        // Construir la representación HTML
        let html = '<div class="account-option">';
        
        // Si tiene código, mostrarlo en negrita
        if (code) {
            html += `<strong>${code}</strong> - `;
        }
        
        // Nombre de la cuenta
        html += `<span>${account.text}</span>`;
        
        // Mostrar la moneda como un badge si está disponible
        if (currency) {
            html += ` <span class="badge badge-light">${currency}</span>`;
        }
        
        html += '</div>';
        
        return $(html);
    }
    
    // Function to initialize account selects with enhanced search
    function initializeAccountSelect(selector) {
        $(selector).select2({
            width: '100%',
            dir: 'rtl',
            language: 'ar',
            placeholder: '@lang('messages.choose_account')',
            allowClear: true,
            templateResult: formatAccountOption,
            escapeMarkup: function(markup) {
                return markup;
            },
            matcher: function(params, data) {
                // Si no hay término de búsqueda, mostrar todos
                if ($.trim(params.term) === '') {
                    return data;
                }
                
                // Si no hay texto en los datos, no hay coincidencia
                if (typeof data.text === 'undefined') {
                    return null;
                }
                
                // El término de búsqueda en minúsculas
                const term = params.term.toLowerCase();
                
                // Textos a buscar
                const text = data.text.toLowerCase();
                const code = $(data.element).data('code') ? $(data.element).data('code').toString().toLowerCase() : '';
                
                // Buscar en texto completo, código o partes separadas
                if (text.indexOf(term) > -1 || code.indexOf(term) > -1) {
                    return data;
                }
                
                // Buscar por palabras individuales
                const words = text.split(/\s+/);
                for (let i = 0; i < words.length; i++) {
                    if (words[i].indexOf(term) > -1) {
                        return data;
                    }
                }
                
                // Sin coincidencia
                return null;
            }
        });
    }
    
    // تهيئة select2 مع خيارات محسنة للبحث
    initializeAccountSelect('#from-account');
    initializeAccountSelect('#to-account');
    
    // ربط الأحداث بعد تهيئة select2
    $('#from-account, #to-account').on('change', function(){ 
        filterTargetAccounts();
        updateAmountTo();
    });
    
    $('#amount_from').on('input', function() {
        updateAmountTo();
        validateAmount();
    });
    
    // تنفيذ أولي
    filterTargetAccounts();
    updateAmountTo();
});
</script>
@endpush 