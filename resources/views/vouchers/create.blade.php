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
                    <div class="card modern-card mb-4">
                        <div class="card-header modern-card-header">
                            <h5 class="mb-0 modern-title"><i class="fas fa-edit mr-2"></i>المعلومات الأساسية</h5>
                        </div>
                        <div class="card-body modern-card-body">
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
                    <div class="card modern-card">
                        <div class="card-header modern-card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0 modern-title"><i class="fas fa-exchange-alt mr-2"></i>الحركات المالية المرتبطة بالسند</h5>
                                </div>
                                <div class="col-auto">
                                    <button type="button" id="add_transaction" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle mr-1"></i>إضافة حركة مالية
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body modern-card-body">
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-lg mr-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1" style="font-weight: 600;">نظام العملات المتعددة</h6>
                                        <p class="mb-0" style="font-size: 14px;">اختر الحساب ثم اختر العملة منفصلة. سيظهر سعر الصرف تلقائياً عند اختلاف العملات ويمكن تعديله.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Transactions Container -->
                            <div id="transactions_container">
                                <!-- Transaction Card Template -->
                                <div class="transaction-card mb-4" data-index="0">
                                    <div class="card modern-card">
                                        <div class="card-header modern-card-header">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h6 class="mb-0 modern-title">
                                                        <i class="fas fa-money-check-alt mr-2"></i>
                                                        الحركة المالية رقم <span class="transaction-number">1</span>
                                                    </h6>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-transaction" title="حذف هذه الحركة">
                                                        <i class="fas fa-trash mr-1"></i>حذف
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body modern-card-body">
                                            <div class="row">
                                                <!-- Cash Account Section -->
                                                <div class="col-md-6 mb-4">
                                                    <div class="account-section account-section-cash">
                                                        <div class="account-section-header">
                                                            <i class="fas fa-wallet mr-2"></i>
                                                            <span>حساب الصندوق</span>
                                                        </div>
                                                        <div class="account-section-body">
                                                            <div class="form-group">
                                                                <label class="modern-label">
                                                                    <i class="fas fa-university mr-1"></i>اختر الصندوق
                                                                </label>
                                                                <select name="transactions[0][account_id]" class="form-control modern-select select2-cash-accounts cash-account-select" data-index="0" required>
                                                                    <option value="">اختر صندوق النقد...</option>
                                                                    @foreach(\App\Models\Account::where('is_cash_box', 1)->get() as $acc)
                                                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label class="modern-label">
                                                                    <i class="fas fa-coins mr-1"></i>عملة الصندوق
                                                                </label>
                                                                <select name="transactions[0][cash_currency]" class="form-control modern-select cash-currency-select" data-index="0" required>
                                                                    <option value="">اختر العملة...</option>
                                                                    @foreach(\App\Models\Currency::all() as $currency)
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
                                                <div class="col-md-6 mb-4">
                                                    <div class="account-section account-section-target">
                                                        <div class="account-section-header">
                                                            <i class="fas fa-user-circle mr-2"></i>
                                                            <span>الحساب المستهدف</span>
                                                        </div>
                                                        <div class="account-section-body">
                                                            <div class="form-group">
                                                                <label class="modern-label">
                                                                    <i class="fas fa-user-tie mr-1"></i>اختر الحساب
                                                                </label>
                                                                <select name="transactions[0][target_account_id]" class="form-control modern-select select2-target-accounts target-account-select" data-index="0" required>
                                                                    <option value="">اختر الحساب المستهدف...</option>
                                                                    @foreach(\App\Models\Account::where('is_group', 0)->where('is_cash_box', 0)->get() as $acc)
                                                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label class="modern-label">
                                                                    <i class="fas fa-dollar-sign mr-1"></i>عملة الحساب
                                                                </label>
                                                                <select name="transactions[0][target_currency]" class="form-control modern-select target-currency-select" data-index="0" required>
                                                                    <option value="">اختر العملة...</option>
                                                                    @foreach(\App\Models\Currency::all() as $currency)
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
                                                    <div class="amount-section amount-section-primary">
                                                        <div class="amount-section-header">
                                                            <i class="fas fa-calculator mr-2"></i>
                                                            <span>المبلغ</span>
                                                        </div>
                                                        <div class="amount-section-body">
                                                            <div class="input-group modern-input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text modern-input-prepend">
                                                                        <i class="fas fa-money-bill-alt"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][amount]" value="{{ old('transactions.0.amount') }}" step="0.001" min="0.01" class="form-control modern-input amount-input" data-index="0" placeholder="أدخل المبلغ..." required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3 exchange-rate-section" style="display: none;">
                                                    <div class="amount-section amount-section-warning">
                                                        <div class="amount-section-header">
                                                            <i class="fas fa-exchange-alt mr-2"></i>
                                                            <span>سعر الصرف</span>
                                                            <small class="badge badge-light ml-2">قابل للتعديل</small>
                                                        </div>
                                                        <div class="amount-section-body">
                                                            <div class="input-group modern-input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text modern-input-prepend">
                                                                        <i class="fas fa-calculator"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][exchange_rate]" step="0.0001" min="0.0001" class="form-control modern-input exchange-rate" data-index="0" placeholder="1310.0000">
                                                            </div>
                                                            <div class="mt-2 text-center">
                                                                <small class="form-text text-muted exchange-note">
                                                                    <i class="fas fa-info-circle mr-1"></i>
                                                                    <span class="current-rate-text">سعر افتراضي - يمكن التعديل</span>
                                                                </small>
                                                                <div class="mt-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary reset-rate" title="إعادة تعيين السعر الافتراضي">
                                                                        <i class="fas fa-undo mr-1"></i>إعادة تعيين
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="amount-section amount-section-success">
                                                        <div class="amount-section-header">
                                                            <i class="fas fa-bullseye mr-2"></i>
                                                            <span>المبلغ النهائي</span>
                                                            <small class="badge badge-light ml-2">تلقائي</small>
                                                        </div>
                                                        <div class="amount-section-body">
                                                            <div class="input-group modern-input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text modern-input-prepend">
                                                                        <i class="fas fa-check-double"></i>
                                                                    </span>
                                                                </div>
                                                                <input type="number" name="transactions[0][converted_amount]" step="0.001" class="form-control modern-input converted-amount" data-index="0" placeholder="المبلغ النهائي..." readonly>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text modern-input-append target-currency-display">
                                                                        ---
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2 text-center">
                                                                <small class="form-text text-muted conversion-note"></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description Section -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label class="modern-label">
                                                            <i class="fas fa-comment-alt mr-1"></i>وصف الحركة المالية
                                                        </label>
                                                        <textarea name="transactions[0][description]" class="form-control modern-textarea" rows="2" placeholder="اكتب وصفاً تفصيلياً لهذه الحركة المالية (اختياري)...">{{ old('transactions.0.description') }}</textarea>
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
                <div class="card-footer bg-white" style="border-top: 1px solid #e0e0e0; padding: 20px;">
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
/* ============================================
   Modern Minimalist Design System
   ============================================ */

/* Typography */
body {
    font-family: 'Tajawal', 'Cairo', sans-serif;
    background-color: #FAFAFA;
    font-size: 14px;
    line-height: 1.6;
    color: #2c3e50;
}

/* Modern Card Styles */
.modern-card {
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    background: #FFFFFF;
    transition: all 0.3s ease;
    margin-bottom: 24px;
}

.modern-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.modern-card-header {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-bottom: 1px solid #e0e0e0;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
}

.modern-card-body {
    padding: 20px;
}

.modern-title {
    font-size: 16px;
    font-weight: 600;
    color: #1976d2;
    margin: 0;
}

/* Account Sections */
.account-section {
    background: #FFFFFF;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.account-section:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.account-section-cash .account-section-header {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    color: #2e7d32;
}

.account-section-target .account-section-header {
    background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
    color: #0277bd;
}

.account-section-header {
    padding: 12px 16px;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 1px solid #e0e0e0;
}

.account-section-body {
    padding: 16px;
}

/* Amount Sections */
.amount-section {
    background: #FFFFFF;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.amount-section:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.amount-section-primary .amount-section-header {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1976d2;
}

.amount-section-warning .amount-section-header {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: #f57c00;
}

.amount-section-success .amount-section-header {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    color: #2e7d32;
}

.amount-section-header {
    padding: 12px 16px;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
}

.amount-section-body {
    padding: 16px;
}

/* Form Controls */
.modern-label {
    font-weight: 600;
    font-size: 14px;
    color: #424242;
    margin-bottom: 8px;
    display: block;
}

.modern-select,
.modern-input,
.modern-textarea {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #FFFFFF;
}

.modern-select:focus,
.modern-input:focus,
.modern-textarea:focus {
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    outline: none;
}

.modern-textarea {
    resize: vertical;
    min-height: 80px;
}

/* Input Groups */
.modern-input-group {
    border-radius: 6px;
    overflow: hidden;
}

.modern-input-prepend,
.modern-input-append {
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    color: #616161;
    font-weight: 500;
}

.modern-input-prepend {
    border-right: none;
}

.modern-input-append {
    border-left: none;
}

/* Buttons */
.btn {
    border-radius: 6px;
    padding: 10px 20px;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-primary {
    background: #2196F3;
    border-color: #2196F3;
    color: #FFFFFF;
}

.btn-primary:hover {
    background: #1976d2;
    border-color: #1976d2;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(33, 150, 243, 0.3);
}

.btn-success {
    background: #4CAF50;
    border-color: #4CAF50;
    color: #FFFFFF;
}

.btn-success:hover {
    background: #388e3c;
    border-color: #388e3c;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
}

.btn-outline-primary {
    border-color: #2196F3;
    color: #2196F3;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #2196F3;
    color: #FFFFFF;
}

.btn-outline-danger {
    border-color: #f44336;
    color: #f44336;
    background: transparent;
}

.btn-outline-danger:hover {
    background: #f44336;
    color: #FFFFFF;
}

.btn-outline-secondary {
    border-color: #757575;
    color: #757575;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #757575;
    color: #FFFFFF;
}

/* Select2 Modern Styling */
.select2-container--bootstrap4 .select2-selection--single {
    height: auto;
    min-height: 42px;
    border: 1px solid #e0e0e0 !important;
    border-radius: 6px !important;
    background: #FFFFFF;
    transition: all 0.2s ease;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection--single {
    border-color: #2196F3 !important;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1) !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    color: #424242;
    padding: 10px 12px;
    padding-right: 30px;
    line-height: 1.5;
    font-size: 14px;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 100%;
    right: 8px;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
    border-color: #757575 transparent transparent transparent;
    border-width: 5px 4px 0 4px;
    margin-top: -2px;
}

/* Select2 Dropdown */
.select2-dropdown {
    border: 1px solid #e0e0e0 !important;
    border-radius: 6px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    margin-top: 4px;
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
}

.select2-search--dropdown .select2-search__field:focus {
    border-color: #2196F3;
    outline: none;
}

.select2-results__option {
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.select2-results__option--highlighted {
    background: #2196F3 !important;
    color: #FFFFFF !important;
}

.select2-results__option[aria-selected="true"] {
    background: #e3f2fd;
    color: #1976d2;
    font-weight: 500;
}

/* Account Option Formatting */
.select2-results__option .account-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.select2-results__option .account-code {
    background: #2196F3;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.select2-results__option--highlighted .account-code {
    background: rgba(255, 255, 255, 0.3);
}

/* Special Inputs */
.exchange-rate {
    text-align: center;
    font-weight: 600;
    background-color: #fff8e1 !important;
    border-color: #ffc107 !important;
    color: #f57c00 !important;
}

.converted-amount {
    font-weight: 600;
    background-color: #e8f5e9 !important;
    color: #2e7d32 !important;
    border-color: #4CAF50 !important;
}

.target-currency-display {
    background-color: #e3f2fd !important;
    color: #1976d2 !important;
    font-weight: 600;
    border-color: #2196F3 !important;
}

/* Alerts */
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.alert-info {
    background: linear-gradient(135deg, #e1f5fe 0%, #b3e5fc 100%);
    color: #0277bd;
    border-left: 4px solid #00BCD4;
}

/* Transaction Card */
.transaction-card {
    transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-card {
        margin: 0 8px 16px 8px;
    }
    
    .modern-card-body {
        padding: 16px;
    }
    
    .account-section-body,
    .amount-section-body {
        padding: 12px;
    }
    
    .col-md-4,
    .col-md-6 {
        margin-bottom: 16px;
    }
    
    .select2-results__option {
        padding: 8px 10px;
        font-size: 13px;
    }
    
    .modern-title {
        font-size: 14px;
    }
    
    .btn {
        padding: 8px 16px;
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    .modern-card-header {
        padding: 12px 16px;
    }
    
    .modern-card-body {
        padding: 12px;
    }
    
    .account-section-header,
    .amount-section-header {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .modern-label {
        font-size: 13px;
    }
    
    .modern-select,
    .modern-input,
    .modern-textarea {
        padding: 8px 10px;
        font-size: 13px;
    }
}
</style>

<script>
$(function(){
    // تهيئة أسعار الصرف من قاعدة البيانات
    @php
        $exchangeRateData = [];
        $currenciesArray = $currencies->toArray();
        
        // البحث عن USD و IQD في العملات
        $usdCurrency = null;
        $iqdCurrency = null;
        foreach ($currenciesArray as $currency) {
            if ($currency['code'] === 'USD') {
                $usdCurrency = $currency;
            }
            if ($currency['code'] === 'IQD') {
                $iqdCurrency = $currency;
            }
        }
        
        // فقط لـ USD <-> IQD: استخدام سعر الصرف مباشرة من جدول USD
        if ($usdCurrency && $iqdCurrency) {
            $usdRate = floatval($usdCurrency['exchange_rate'] ?? 1400);
            
            // USD → IQD: السعر هو exchange_rate من USD مباشرة (مثلاً 1400)
            $exchangeRateData['USD_IQD'] = [
                'rate' => $usdRate,
                'display' => '1 دولار = ' . number_format($usdRate, 0) . ' دينار',
                'inverse' => false
            ];
            
            // IQD → USD: نفس السعر (1400) لأن المستخدم يريد أن يظهر 1400 دائماً
            $exchangeRateData['IQD_USD'] = [
                'rate' => $usdRate,
                'display' => number_format($usdRate, 0) . ' دينار = 1 دولار',
                'inverse' => true
            ];
        }
    @endphp
    
    const exchangeRateData = @json($exchangeRateData);
    
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
            minimumResultsForSearch: 0,
            templateResult: formatAccountOption,
            templateSelection: formatAccountSelection,
            escapeMarkup: function(markup) { return markup; },
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            }
        });
    };

    // تنسيق عرض الحسابات في القائمة - تصميم حديث
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        if (account.text.includes(' - ')) {
            const parts = account.text.split(' - ');
            return `
                <div class="account-option">
                    <span class="account-name">${parts[1]}</span>
                    <span class="account-code">${parts[0]}</span>
                </div>
            `;
        }
        
        return account.text;
    }

    // تنسيق عرض الحساب المختار - تصميم حديث
    function formatAccountSelection(account) {
        if (!account.id) return account.text;
        
        if (account.text.includes(' - ')) {
            const parts = account.text.split(' - ');
            return `<span class="text-dark">${parts[0]}</span> - <span class="text-muted">${parts[1]}</span>`;
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
                // عملات مختلفة - التحقق إذا كانت USD <-> IQD فقط
                const isUsdIqd = (cashCurrency === 'USD' && targetCurrency === 'IQD') || 
                                (cashCurrency === 'IQD' && targetCurrency === 'USD');
                
                if (isUsdIqd) {
                    // USD <-> IQD: إظهار قسم سعر الصرف
                    exchangeRateSection.slideDown(300);
                    
                    // تحديد بيانات سعر الصرف
                    const rateKey = `${cashCurrency}_${targetCurrency}`;
                    let rateData = exchangeRateData[rateKey];
                    
                    if (!rateData) {
                        // إذا لم توجد في البيانات، جلب من data-rate في select
                        const cashCurrencySelect = card.find('.cash-currency-select');
                        const targetCurrencySelect = card.find('.target-currency-select');
                        const cashOption = cashCurrencySelect.find('option:selected');
                        const targetOption = targetCurrencySelect.find('option:selected');
                        
                        let rate = 1400; // افتراضي
                        if (cashCurrency === 'USD' && cashOption.length) {
                            rate = parseFloat(cashOption.data('rate')) || 1400;
                        } else if (targetCurrency === 'USD' && targetOption.length) {
                            rate = parseFloat(targetOption.data('rate')) || 1400;
                        }
                        
                        rateData = {
                            rate: rate,
                            display: cashCurrency === 'USD' 
                                ? `1 دولار = ${rate.toFixed(0)} دينار`
                                : `${rate.toFixed(0)} دينار = 1 دولار`,
                            inverse: cashCurrency === 'IQD'
                        };
                    }
                    
                    // استخدام السعر مباشرة (1400) بدون قسمة
                    const displayRate = rateData.rate.toFixed(4);
                    
                    // تعيين السعر إذا كان الحقل فارغاً أو يحتوي على القيمة الافتراضية
                    if (!exchangeRateField.val() || exchangeRateField.val() === '1' || exchangeRateField.val() === '') {
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
                } else {
                    // عملات أخرى غير USD/IQD: إخفاء قسم سعر الصرف
                    exchangeRateSection.slideUp(300);
                    exchangeRateField.val('1');
                }
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