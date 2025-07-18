@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1><i class="fas fa-file-invoice-dollar mr-2"></i>@lang('messages.create_new_financial_voucher')</h1>
                <p class="text-muted">إنشاء سند مالي جديد مع دعم العملات المتعددة</p>
            </div>
            <div class="col-sm-4 text-left">
                <a href="{{ Route::localizedRoute('vouchers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> @lang('messages.return_to_vouchers')
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Form Card -->
        <div class="card shadow">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-info-circle mr-2 text-primary"></i>@lang('messages.voucher_information')
                        </h3>
                        <p class="text-muted mb-0 mt-1">قم بملء جميع المعلومات المطلوبة لإنشاء السند</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ Route::localizedRoute('vouchers.store') }}" method="POST" id="voucherForm">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-1">@lang('messages.validation_errors')</h5>
                                    <ul class="mb-0 list-unstyled">
                                        @foreach($errors->all() as $error)
                                            <li><i class="fas fa-dot-circle mr-1"></i>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>المعلومات الأساسية</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="voucher_type" class="font-weight-bold">
                                        <i class="fas fa-tags mr-1 text-primary"></i>@lang('messages.voucher_type')
                                    </label>
                                    <select name="type" id="voucher_type" class="form-control select2" required>
                                        <option value="" disabled {{ old('type')? '':'selected' }}>@lang('messages.choose_type')</option>
                                        <option value="receipt" {{ old('type')=='receipt'?'selected':'' }}>@lang('messages.receipt_voucher')</option>
                                        <option value="payment" {{ old('type')=='payment'?'selected':'' }}>@lang('messages.payment_voucher')</option>
                                        <option value="deposit" {{ old('type')=='deposit'?'selected':'' }}>@lang('messages.voucher_deposit')</option>
                                        <option value="withdraw" {{ old('type')=='withdraw'?'selected':'' }}>@lang('messages.voucher_withdraw')</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="voucher_date" class="font-weight-bold">
                                        <i class="fas fa-calendar-alt mr-1 text-success"></i>@lang('messages.voucher_date')
                                    </label>
                                    <input type="datetime-local" name="date" id="voucher_date" class="form-control" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="recipient_name" class="font-weight-bold">
                                        <i class="fas fa-user mr-1 text-info"></i>@lang('messages.recipient_payer_name')
                                    </label>
                                    <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name') }}" placeholder="@lang('messages.recipient_payer_placeholder_name')" required>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label for="voucher_description" class="font-weight-bold">
                                    <i class="fas fa-comment-alt mr-1 text-secondary"></i>@lang('messages.general_voucher_description')
                                </label>
                                <textarea name="description" id="voucher_description" rows="3" class="form-control" placeholder="اكتب وصفاً مفصلاً للسند المالي..." required>{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Section -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0"><i class="fas fa-exchange-alt mr-2"></i>الحركات المالية المرتبطة بالسند</h5>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="add_transaction" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle mr-1"></i>إضافة حركة مالية
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x text-info mr-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">نظام العملات المتعددة</h6>
                                        <p class="mb-0">اختر الحساب ثم اختر العملة منفصلة. سيظهر سعر الصرف تلقائياً عند اختلاف العملات ويمكن تعديله.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Transactions Container -->
                            <div id="transactions_container">
                                <!-- Transaction Card Template -->
                                <div class="transaction-card mb-4" data-index="0">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-money-check-alt mr-2"></i>
                                                        الحركة المالية رقم <span class="transaction-number">1</span>
                                                    </h6>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-light btn-sm remove-transaction" title="حذف هذه الحركة">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Cash Account Section -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="card bg-light">
                                                        <div class="card-header bg-success text-white py-2">
                                                            <h6 class="mb-0"><i class="fas fa-wallet mr-2"></i>حساب الصندوق</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group mb-3">
                                                                <label class="font-weight-bold">
                                                                    <i class="fas fa-university mr-1"></i>اختر الصندوق
                                                                </label>
                                                                <select name="transactions[0][account_id]" class="form-control select2-cash-accounts cash-account-select" data-index="0" required>
                                                                    <option value="">اختر صندوق النقد...</option>
                                                                    @foreach(\App\Models\Account::where('is_cash_box', 1)->get() as $acc)
                                                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold">
                                                                    <i class="fas fa-coins mr-1"></i>عملة الصندوق
                                                                </label>
                                                                <select name="transactions[0][cash_currency]" class="form-control cash-currency-select" data-index="0" required>
                                                                    <option value="">اختر العملة...</option>
                                                                    @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                                                        <option value="{{ $currency->code }}" data-rate="{{ $currency->exchange_rate }}">
                                                                            {{ $currency->code }} - {{ $currency->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Target Account Section -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="card bg-light">
                                                        <div class="card-header bg-info text-white py-2">
                                                            <h6 class="mb-0"><i class="fas fa-user-circle mr-2"></i>الحساب المستهدف</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group mb-3">
                                                                <label class="font-weight-bold">
                                                                    <i class="fas fa-user-tie mr-1"></i>اختر الحساب
                                                                </label>
                                                                <select name="transactions[0][target_account_id]" class="form-control select2-target-accounts target-account-select" data-index="0" required>
                                                                    <option value="">اختر الحساب المستهدف...</option>
                                                                    @foreach(\App\Models\Account::where('is_group', 0)->where('is_cash_box', 0)->get() as $acc)
                                                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold">
                                                                    <i class="fas fa-dollar-sign mr-1"></i>عملة الحساب
                                                                </label>
                                                                <select name="transactions[0][target_currency]" class="form-control target-currency-select" data-index="0" required>
                                                                    <option value="">اختر العملة...</option>
                                                                    @foreach(\App\Models\Currency::where('is_active', true)->get() as $currency)
                                                                        <option value="{{ $currency->code }}" data-rate="{{ $currency->exchange_rate }}">
                                                                            {{ $currency->code }} - {{ $currency->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Amount and Exchange Section -->
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="card bg-warning text-dark">
                                                        <div class="card-header bg-warning text-dark py-2">
                                                            <h6 class="mb-0"><i class="fas fa-calculator mr-2"></i>المبلغ</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-warning border-warning">
                                                                        <i class="fas fa-money-bill-alt"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][amount]" value="{{ old('transactions.0.amount') }}" step="0.001" min="0.01" class="form-control amount-input" data-index="0" placeholder="أدخل المبلغ..." required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3 exchange-rate-section" style="display: none;">
                                                    <div class="card bg-warning text-dark">
                                                        <div class="card-header bg-warning text-dark py-2">
                                                            <h6 class="mb-0 font-weight-bold">
                                                                <i class="fas fa-exchange-alt mr-2"></i>سعر الصرف
                                                                <small class="badge badge-dark ml-2">قابل للتعديل</small>
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-warning border-warning text-dark font-weight-bold">
                                                                        <i class="fas fa-calculator"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][exchange_rate]" step="0.0001" min="0.0001" class="form-control exchange-rate font-weight-bold" data-index="0" placeholder="1310.0000">
                                                            </div>
                                                            <div class="mt-2 text-center">
                                                                <small class="form-text text-dark exchange-note font-weight-medium">
                                                                    <i class="fas fa-info-circle mr-1"></i>
                                                                    <span class="current-rate-text">سعر افتراضي - يمكن التعديل</span>
                                                                </small>
                                                                <div class="mt-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-dark reset-rate" title="إعادة تعيين السعر الافتراضي">
                                                                        <i class="fas fa-undo mr-1"></i>إعادة تعيين
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="card bg-success text-white">
                                                        <div class="card-header bg-success text-white py-2">
                                                            <h6 class="mb-0 font-weight-bold">
                                                                <i class="fas fa-bullseye mr-2"></i>المبلغ النهائي
                                                                <small class="badge badge-light text-success ml-2">تلقائي</small>
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-success border-success text-white font-weight-bold">
                                                                        <i class="fas fa-check-double"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][converted_amount]" step="0.001" class="form-control converted-amount font-weight-bold" data-index="0" placeholder="المبلغ النهائي..." readonly>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text bg-light text-success border-success target-currency-display font-weight-bold">
                                                                        ---
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2 text-center">
                                                                <small class="form-text text-white conversion-note font-weight-medium"></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description Section -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold text-primary">
                                                            <i class="fas fa-comment-alt mr-1"></i>وصف الحركة المالية
                                                        </label>
                                                        <textarea name="transactions[0][description]" class="form-control" rows="2" placeholder="اكتب وصفاً تفصيلياً لهذه الحركة المالية (اختياري)...">{{ old('transactions.0.description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="card-footer bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <button type="button" id="add_transaction" class="btn btn-outline-primary">
                                <i class="fas fa-plus-circle mr-2"></i>إضافة حركة مالية جديدة
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success px-5" id="submitBtn">
                                <i class="fas fa-save mr-2"></i>@lang('messages.save_voucher')
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* تحسين التصميم العام */
body {
    font-family: 'Tajawal', 'Cairo', sans-serif;
    background-color: #f8f9fa;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid #dee2e6;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 5px;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px);
    border: 1px solid #ced4da;
    border-radius: 5px;
}

.alert {
    border-radius: 5px;
}

.transaction-card {
    transition: all 0.3s ease;
}

.transaction-card:hover {
    transform: translateY(-2px);
}

.exchange-rate-section {
    transition: all 0.3s ease;
}

.exchange-rate {
    text-align: center;
    font-weight: bold;
    background-color: #fff3cd !important;
    border-color: #ffc107 !important;
    color: #856404 !important;
}

.converted-amount {
    font-weight: bold;
    background-color: #d4edda !important;
    color: #155724;
}

.target-currency-display {
    background-color: #e3f2fd !important;
    color: #1976d2 !important;
    font-weight: bold;
}

.conversion-note {
    font-size: 0.8rem;
    margin-top: 5px;
}

/* تحسين مظهر Select2 */
.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.select2-results__option {
    padding: 8px 12px;
}

.select2-results__option--highlighted {
    background-color: #007bff !important;
    color: white !important;
}

.select2-results__option .badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

/* تحسين عرض الحسابات المختارة */
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    color: #495057;
    padding-left: 12px;
    padding-right: 20px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    line-height: 1.5;
    padding-top: 0.375rem;
    padding-bottom: 0.375rem;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem);
}

/* تحسين عرض الرقم والاسم في الحسابات */
.account-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.account-code {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 0.8rem;
    margin-left: 8px;
}

.account-name {
    flex: 1;
    color: #495057;
}

@media (max-width: 768px) {
    .card {
        margin: 0 10px;
    }
    
    .form-row .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .select2-results__option {
        padding: 6px 8px;
        font-size: 0.9rem;
    }
    
    .select2-results__option .badge {
        font-size: 0.7rem;
    }
}
</style>

<script>
$(function(){
    // تهيئة المتغيرات مع أسعار الصرف المحسنة للوضوح
    const exchangeRateData = {
        'USD_IQD': { rate: 1310.0000, display: '1 دولار = 1310 دينار' },
        'IQD_USD': { rate: 1310.0000, display: '1310 دينار = 1 دولار', inverse: true },
        'EUR_IQD': { rate: 1420.0000, display: '1 يورو = 1420 دينار' },
        'IQD_EUR': { rate: 1420.0000, display: '1420 دينار = 1 يورو', inverse: true },
        'USD_EUR': { rate: 0.9200, display: '1 دولار = 0.92 يورو' },
        'EUR_USD': { rate: 1.0870, display: '1 يورو = 1.087 دولار' }
    };
    
    let idx = 1;

    // إعداد Select2 محسن
    const initializeSelect2 = () => {
        $('#voucher_type').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        $('.select2-cash-accounts, .select2-target-accounts').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'اختر الحساب...',
            allowClear: true,
            templateResult: formatAccountOption,
            templateSelection: formatAccountSelection,
            escapeMarkup: function(markup) { return markup; }
        });
    };

    // تنسيق عرض الحسابات في القائمة
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        if (account.text.includes(' - ')) {
            const parts = account.text.split(' - ');
            return `
                <div class="d-flex align-items-center py-1">
                    <span class="badge badge-primary mr-2" style="min-width: 60px; font-size: 0.8rem;">${parts[0]}</span>
                    <span class="text-dark">${parts[1]}</span>
                </div>
            `;
        }
        
        return account.text;
    }

    // تنسيق عرض الحساب المختار
    function formatAccountSelection(account) {
        if (!account.id) return account.text;
        
        if (account.text.includes(' - ')) {
            const parts = account.text.split(' - ');
            return `${parts[0]} - ${parts[1]}`;
        }
        
        return account.text;
    }

    // حساب وتحديث سعر الصرف بطريقة محسنة وأوضح
    function updateExchangeRate(card) {
        const cashCurrency = card.find('.cash-currency-select').val();
        const targetCurrency = card.find('.target-currency-select').val();
        const exchangeRateField = card.find('.exchange-rate');
        const exchangeRateSection = card.find('.exchange-rate-section');
        const exchangeNote = card.find('.current-rate-text');
        const targetCurrencyDisplay = card.find('.target-currency-display');
        
        if (cashCurrency && targetCurrency) {
            // تحديث عرض العملة المستهدفة
            targetCurrencyDisplay.text(targetCurrency);
            
            if (cashCurrency === targetCurrency) {
                // نفس العملة - إخفاء قسم سعر الصرف
                exchangeRateSection.slideUp(300);
                exchangeRateField.val('1');
                showNotification('نفس العملة - لا حاجة للتحويل', 'success');
            } else {
                // عملات مختلفة - إظهار قسم سعر الصرف مع عرض محسن
                exchangeRateSection.slideDown(300);
                
                // تحديد بيانات سعر الصرف
                const rateKey = `${cashCurrency}_${targetCurrency}`;
                let rateData = exchangeRateData[rateKey];
                
                if (!rateData) {
                    // قيمة افتراضية إذا لم توجد
                    rateData = { rate: 1.0000, display: `1 ${cashCurrency} = 1 ${targetCurrency}`, inverse: false };
                }
                
                // حساب السعر المناسب للعرض
                let displayRate;
                if (rateData.inverse) {
                    // للعملات المقلوبة مثل IQD → USD، نعرض السعر الأساسي
                    displayRate = rateData.rate.toFixed(4);
                } else {
                    displayRate = rateData.rate.toFixed(4);
                }
                
                // تعيين السعر إذا كان الحقل فارغاً أو يحتوي على القيمة الافتراضية
                if (!exchangeRateField.val() || exchangeRateField.val() === '1') {
                    exchangeRateField.val(displayRate);
                }
                
                // عرض الملاحظة بشكل واضح
                let noteText;
                if (rateData.inverse) {
                    noteText = `${rateData.display} (السعر: ${displayRate})`;
                } else {
                    noteText = `${rateData.display}`;
                }
                
                exchangeNote.html(`
                    <span class="text-warning">💰</span>
                    ${noteText}
                `);
                
                // تخزين السعر الافتراضي في البيانات
                card.data('default-rate', displayRate);
                card.data('rate-info', rateData);
                
                showNotification(`💱 ${rateData.display}`, 'info');
            }
        } else {
            exchangeRateSection.hide();
            exchangeRateField.val('');
            targetCurrencyDisplay.text('---');
        }
        
        updateConvertedAmount(card);
    }

    // حساب المبلغ النهائي مع معالجة محسنة للعملات المقلوبة
    function updateConvertedAmount(card) {
        const amount = parseFloat(card.find('.amount-input').val()) || 0;
        const exchangeRate = parseFloat(card.find('.exchange-rate').val()) || 1;
        const cashCurrency = card.find('.cash-currency-select').val();
        const targetCurrency = card.find('.target-currency-select').val();
        const rateInfo = card.data('rate-info');
        
        let convertedAmount;
        
        // حساب المبلغ النهائي حسب نوع التحويل
        if (rateInfo && rateInfo.inverse) {
            // للعملات المقلوبة مثل IQD → USD
            convertedAmount = (amount / exchangeRate).toFixed(3);
        } else {
            // للعملات العادية مثل USD → IQD  
            convertedAmount = (amount * exchangeRate).toFixed(3);
        }
        
        card.find('.converted-amount').val(convertedAmount);
        
        // تحديث ملاحظة التحويل
        const conversionNote = card.find('.conversion-note');
        if (cashCurrency && targetCurrency && cashCurrency !== targetCurrency) {
            let calculationDisplay;
            if (rateInfo && rateInfo.inverse) {
                calculationDisplay = `
                    <div class="text-center">
                        <span class="badge badge-light text-dark mr-1">${formatNumber(amount)} ${cashCurrency}</span>
                        <i class="fas fa-divide text-warning mx-1"></i>
                        <span class="badge badge-warning text-dark mr-1">${exchangeRate}</span>
                        <i class="fas fa-equals text-light mx-1"></i>
                        <span class="badge badge-light text-success">${formatNumber(convertedAmount)} ${targetCurrency}</span>
                    </div>
                `;
            } else {
                calculationDisplay = `
                    <div class="text-center">
                        <span class="badge badge-light text-dark mr-1">${formatNumber(amount)} ${cashCurrency}</span>
                        <i class="fas fa-times text-warning mx-1"></i>
                        <span class="badge badge-warning text-dark mr-1">${exchangeRate}</span>
                        <i class="fas fa-equals text-light mx-1"></i>
                        <span class="badge badge-light text-success">${formatNumber(convertedAmount)} ${targetCurrency}</span>
                    </div>
                `;
            }
            
            conversionNote.html(calculationDisplay);
        } else {
            conversionNote.html(`
                <div class="text-center">
                    <i class="fas fa-check-circle text-light mr-1"></i>
                    <span class="text-light">نفس العملة - المبلغ كما هو</span>
                </div>
            `);
        }
    }

    // ترقيم البطاقات
    function updateCardNumbers() {
        $('#transactions_container .transaction-card').each(function(index) {
            $(this).find('.transaction-number').text(index + 1);
            $(this).attr('data-index', index);
            
            // تحديث أسماء الحقول
            $(this).find('select, input, textarea').each(function() {
                const name = $(this).attr('name');
                if (name && name.includes('transactions[')) {
                    const newName = name.replace(/transactions\[\d+\]/, 'transactions[' + index + ']');
                    $(this).attr('name', newName);
                    $(this).attr('data-index', index);
                }
            });
        });
    }

    // تنسيق الأرقام
    function formatNumber(number) {
        return new Intl.NumberFormat('ar-IQ').format(number);
    }

    // عرض الإشعارات
    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('body').append(notification);
        setTimeout(() => notification.alert('close'), 3000);
    }

    // معالجة الأحداث
    $(document).on('change', '.cash-currency-select, .target-currency-select', function() {
        const card = $(this).closest('.transaction-card');
        updateExchangeRate(card);
    });

    $(document).on('input change', '.amount-input, .exchange-rate', function() {
        const card = $(this).closest('.transaction-card');
        updateConvertedAmount(card);
    });

    // إعادة تعيين سعر الصرف
    $(document).on('click', '.reset-rate', function() {
        const card = $(this).closest('.transaction-card');
        const defaultRate = card.data('default-rate');
        
        if (defaultRate) {
            card.find('.exchange-rate').val(defaultRate);
            updateConvertedAmount(card);
            showNotification('تم إعادة تعيين سعر الصرف للقيمة الافتراضية', 'success');
        }
    });

    // إضافة بطاقة جديدة
    $('#add_transaction').click(function(){
        const $template = $('#transactions_container .transaction-card:first');
        const $new = $template.clone();
        
        // مسح القيم
        $new.find('select, input, textarea').each(function(){
            $(this).val('').trigger('change');
        });
        
        // إخفاء قسم سعر الصرف في البطاقة الجديدة
        $new.find('.exchange-rate-section').hide();
        
        $('#transactions_container').append($new);
        updateCardNumbers();
        initializeSelect2();
        
        showNotification('تم إضافة حركة مالية جديدة', 'success');
    });

    // حذف بطاقة
    $(document).on('click', '.remove-transaction', function(){
        const $card = $(this).closest('.transaction-card');
        
        if($('#transactions_container .transaction-card').length > 1) {
            if(confirm('هل أنت متأكد من حذف هذه الحركة المالية؟')) {
                $card.remove();
                updateCardNumbers();
                showNotification('تم حذف الحركة المالية', 'success');
            }
        } else {
            showNotification('يجب أن يكون هناك حركة مالية واحدة على الأقل', 'error');
        }
    });

    // إرسال النموذج
    $('#voucherForm').on('submit', function(e) {
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>جاري الحفظ...');
        
        // التحقق من صحة البيانات
        let isValid = true;
        $('.amount-input').each(function() {
            if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                isValid = false;
                $(this).addClass('is-invalid');
                showNotification('يجب إدخال مبلغ صحيح لجميع الحركات', 'error');
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            submitBtn.prop('disabled', false).html(originalText);
            return false;
        }
        
        showNotification('جاري حفظ السند المالي...', 'info');
    });

    // تهيئة أولية
    initializeSelect2();
    
    // رسالة ترحيب
    setTimeout(() => {
        showNotification('مرحباً بك في نظام السندات متعدد العملات!', 'success');
    }, 500);
});
</script>
@endpush