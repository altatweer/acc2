@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1><i class="fas fa-exchange-alt mr-2"></i>@lang('messages.add_transfer_voucher_between_accounts')</h1>
                <p class="text-muted">تحويل مبلغ بين صناديق نقدية مع دعم العملات المتعددة</p>
            </div>
            <div class="col-sm-4 text-left">
                <a href="{{ Route::localizedRoute('vouchers.index', ['type' => 'transfer']) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> @lang('messages.back')
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
                            <i class="fas fa-info-circle mr-2 text-primary"></i>معلومات سند التحويل
                        </h3>
                        <p class="text-muted mb-0 mt-1">قم بملء جميع المعلومات المطلوبة لإنشاء سند التحويل</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ Route::localizedRoute('vouchers.transfer.store') }}" id="transferForm">
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
                                <div class="form-group col-md-12">
                                    <label for="voucher_date" class="modern-label">
                                        <i class="fas fa-calendar-alt mr-1 text-success"></i>@lang('messages.voucher_date')
                                    </label>
                                    <input type="datetime-local" name="date" id="voucher_date" class="form-control modern-input" value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('date')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accounts Section -->
                    <div class="card modern-card mb-4">
                        <div class="card-header modern-card-header">
                            <h5 class="mb-0 modern-title"><i class="fas fa-university mr-2"></i>الحسابات</h5>
                        </div>
                        <div class="card-body modern-card-body">
                            <div class="row">
                                <!-- Source Account Section -->
                                <div class="col-md-6 mb-4">
                                    <div class="account-section account-section-cash">
                                        <div class="account-section-header">
                                            <i class="fas fa-wallet mr-2"></i>
                                            <span>@lang('messages.source_account')</span>
                                        </div>
                                        <div class="account-section-body">
                                            <div class="form-group">
                                                <label class="modern-label">
                                                    <i class="fas fa-university mr-1"></i>اختر الصندوق المصدر
                                                </label>
                                                <select name="account_id" class="form-control modern-select select2-account" required id="from-account">
                                                    <option value="">@lang('messages.choose_account')</option>
                                                    @foreach($cashAccountsFrom as $acc)
                                                        <option value="{{ $acc->id }}" data-currency="{{ $acc->default_currency ?? '' }}" data-code="{{ $acc->code ?? '' }}" data-name="{{ $acc->name }}">
                                                            {{ $acc->code ? $acc->code.' - ' : '' }}{{ $acc->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="modern-label">
                                                    <i class="fas fa-coins mr-1"></i>عملة الصندوق
                                                </label>
                                                <select name="cash_currency" class="form-control modern-select cash-currency-select" id="cash-currency" required>
                                                    <option value="">اختر العملة...</option>
                                                    @foreach(\App\Models\Currency::all() as $currency)
                                                        <option value="{{ $currency->code }}" data-rate="{{ $currency->exchange_rate }}">
                                                            {{ $currency->code }} - {{ $currency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('cash_currency')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Target Account Section -->
                                <div class="col-md-6 mb-4">
                                    <div class="account-section account-section-target">
                                        <div class="account-section-header">
                                            <i class="fas fa-user-circle mr-2"></i>
                                            <span>@lang('messages.target_account')</span>
                                        </div>
                                        <div class="account-section-body">
                                            <div class="form-group">
                                                <label class="modern-label">
                                                    <i class="fas fa-user-tie mr-1"></i>اختر الصندوق المستهدف
                                                </label>
                                                <select name="target_account_id" class="form-control modern-select select2-account" required id="to-account">
                                                    <option value="">@lang('messages.choose_account')</option>
                                                    @foreach($cashAccountsTo as $acc)
                                                        <option value="{{ $acc->id }}" data-currency="{{ $acc->default_currency ?? '' }}" data-code="{{ $acc->code ?? '' }}" data-name="{{ $acc->name }}">
                                                            {{ $acc->code ? $acc->code.' - ' : '' }}{{ $acc->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('target_account_id')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="modern-label">
                                                    <i class="fas fa-dollar-sign mr-1"></i>عملة الحساب
                                                </label>
                                                <select name="target_currency" class="form-control modern-select target-currency-select" id="target-currency" required>
                                                    <option value="">اختر العملة...</option>
                                                    @foreach(\App\Models\Currency::all() as $currency)
                                                        <option value="{{ $currency->code }}" data-rate="{{ $currency->exchange_rate }}">
                                                            {{ $currency->code }} - {{ $currency->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('target_currency')<span class="text-danger">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount and Exchange Section -->
                    <div class="card modern-card">
                        <div class="card-header modern-card-header">
                            <h5 class="mb-0 modern-title"><i class="fas fa-money-bill-wave mr-2"></i>المبلغ وسعر الصرف</h5>
                        </div>
                        <div class="card-body modern-card-body">
                            <div class="row">
                                <!-- Amount From -->
                                <div class="col-md-4 mb-3">
                                    <div class="amount-section amount-section-primary">
                                        <div class="amount-section-header">
                                            <i class="fas fa-calculator mr-2"></i>
                                            <span>@lang('messages.transferred_amount')</span>
                                        </div>
                                        <div class="amount-section-body">
                                            <div class="input-group modern-input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text modern-input-prepend">
                                                        <i class="fas fa-money-bill-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="amount" id="amount_from" class="form-control modern-input" min="0.01" step="0.01" placeholder="أدخل المبلغ..." required>
                                            </div>
                                            <div id="current-balance-info" class="mt-2" style="display:none;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exchange Rate -->
                                <div class="col-md-4 mb-3 exchange-rate-section" id="exchange_rate_group" style="display:none;">
                                    <div class="amount-section amount-section-warning">
                                        <div class="amount-section-header">
                                            <i class="fas fa-exchange-alt mr-2"></i>
                                            <span>@lang('messages.exchange_rate')</span>
                                            <small class="badge badge-light ml-2">قابل للتعديل</small>
                                        </div>
                                        <div class="amount-section-body">
                                            <div class="input-group modern-input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text modern-input-prepend">
                                                        <i class="fas fa-calculator"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="exchange_rate" id="exchange_rate" class="form-control modern-input exchange-rate" step="0.0001" min="0.0001" placeholder="1400.0000">
                                            </div>
                                            <div class="mt-2 text-center">
                                                <small class="form-text text-muted" id="exchange-rate-note">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    <span id="current-rate-text">سعر افتراضي - يمكن التعديل</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount To -->
                                <div class="col-md-4 mb-3" id="amount_to_group" style="display:none;">
                                    <div class="amount-section amount-section-success">
                                        <div class="amount-section-header">
                                            <i class="fas fa-bullseye mr-2"></i>
                                            <span>@lang('messages.received_amount')</span>
                                            <small class="badge badge-light ml-2">تلقائي</small>
                                        </div>
                                        <div class="amount-section-body">
                                            <div class="input-group modern-input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text modern-input-prepend">
                                                        <i class="fas fa-check-double"></i>
                                                    </span>
                                                </div>
                                                <input type="number" id="amount_to" class="form-control modern-input converted-amount" placeholder="المبلغ النهائي..." readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text modern-input-append target-currency-display" id="target-currency-display">
                                                        ---
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <div id="same-currency-alert" class="alert alert-warning mt-3" style="display:none;">
                        <i class="fas fa-exclamation-triangle mr-2"></i>@lang('messages.same_account_alert')
                    </div>
                    <div id="no-cashbox-alert" class="alert alert-warning mt-3" style="display:none;"></div>
                    <div id="insufficient-balance-alert" class="alert alert-danger mt-3" style="display:none;"></div>
                    <div id="balance-error-alert" class="alert alert-warning mt-3" style="display:none;"></div>
                </div>
                
                <!-- Form Actions -->
                <div class="card-footer bg-white" style="border-top: 1px solid #e0e0e0; padding: 20px;">
                    <div class="row align-items-center">
                        <div class="col">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success px-5" id="save-btn">
                                <i class="fas fa-save mr-2"></i>@lang('messages.save_transfer_voucher')
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

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
    z-index: 9999;
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

.alert-warning {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: #f57c00;
    border-left: 4px solid #ff9800;
}

.alert-danger {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    color: #c62828;
    border-left: 4px solid #f44336;
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
$(document).ready(function(){
    // تهيئة أسعار الصرف من قاعدة البيانات
    @php
        $exchangeRateData = [];
        $currencies = \App\Models\Currency::all();
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
    
    // Obtener los balances de los cashboxes
    let cashAccounts = @json($cashAccountsFrom->concat($cashAccountsTo));
    let exchangeRates = @json($exchangeRates);
    
    // Agregar información de saldo a los cashAccounts
    cashAccounts = cashAccounts.map(account => {
        if (account.balance === undefined || account.balance === null) {
            account.balance = 0;
        }
        return account;
    });
    
    // تنسيق عرض الحسابات في القائمة - تصميم حديث
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        const code = $(account.element).data('code');
        const name = $(account.element).data('name') || account.text;
        const currency = $(account.element).data('currency');
        
        if (code) {
            return `
                <div class="account-option">
                    <span class="account-name">${name}</span>
                    <span class="account-code">${code}</span>
                    ${currency ? `<span class="badge badge-light ml-2">${currency}</span>` : ''}
                </div>
            `;
        }
        
        return account.text;
    }

    // تنسيق عرض الحساب المختار - تصميم حديث
    function formatAccountSelection(account) {
        if (!account.id) return account.text;
        
        const code = $(account.element).data('code');
        const name = $(account.element).data('name') || account.text;
        
        if (code) {
            return `<span class="text-dark">${code}</span> - <span class="text-muted">${name}</span>`;
        }
        
        return account.text;
    }
    
    // Function to initialize account selects with enhanced search
    function initializeAccountSelect(selector) {
        $(selector).select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: 'rtl',
            language: 'ar',
            placeholder: '@lang('messages.choose_account')',
            allowClear: true,
            dropdownParent: $('body'),
            templateResult: formatAccountOption,
            templateSelection: formatAccountSelection,
            escapeMarkup: function(markup) {
                return markup;
            },
            closeOnSelect: true
        });
    }
    
    // تهيئة select2 للحسابات
    initializeAccountSelect('#from-account');
    initializeAccountSelect('#to-account');
    
    // تهيئة select2 للعملات
    $('#cash-currency, #target-currency').select2({
        theme: 'bootstrap4',
        width: '100%',
        dir: 'rtl',
        language: 'ar',
        placeholder: 'اختر العملة...',
        allowClear: true,
        dropdownParent: $('body'),
        closeOnSelect: true
    });
    
    // عند اختيار حساب، تحديد العملة الافتراضية
    $('#from-account').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const defaultCurrency = selectedOption.data('currency');
        if (defaultCurrency) {
            $('#cash-currency').val(defaultCurrency).trigger('change');
        }
    });
    
    $('#to-account').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const defaultCurrency = selectedOption.data('currency');
        if (defaultCurrency) {
            $('#target-currency').val(defaultCurrency).trigger('change');
        }
    });
    

    function filterTargetAccounts() {
        const fromVal = $('#from-account').val();
        const toVal = $('#to-account').val();
        
        // Destroy and rebuild to-account Select2
        if ($('#to-account').hasClass('select2-hidden-accessible')) {
            $('#to-account').select2('destroy');
        }
        
        $('#to-account option').each(function(){
            if (!$(this).val()) return;
            if ($(this).val() === fromVal) {
                $(this).prop('disabled', true).hide();
            } else {
                $(this).prop('disabled', false).show();
            }
        });
        
        // Reinitialize select2
        initializeAccountSelect('#to-account');
        
        // If the previously selected "to" account is now disabled, clear the selection
        if (toVal === fromVal) {
            $('#to-account').val('').trigger('change');
        }
        
        // تحديث واجهة التحويل
        const saveBtn = $('#save-btn');
        const sameAlert = $('#same-currency-alert');
        
        if (fromVal && toVal && fromVal === toVal) {
            saveBtn.prop('disabled', true);
            sameAlert.show();
        } else {
            saveBtn.prop('disabled', false);
            sameAlert.hide();
        }
        
        // Si hay una cuenta de origen seleccionada, عرض الرصيد
        if (fromVal) {
            const currency = $('#cash-currency').val();
            updateBalanceDisplay(currency);
        }
        
        // تحديث عرض سعر الصرف
        updateExchangeRate();
    }
    
    // تحديث عرض سعر الصرف بناءً على العملات المختارة
    function updateExchangeRate() {
        const fromCurrency = $('#cash-currency').val();
        const toCurrency = $('#target-currency').val();
        updateExchangeRateDisplay(fromCurrency, toCurrency);
    }
    
    // تحديث عرض سعر الصرف
    function updateExchangeRateDisplay(fromCurrency, toCurrency) {
        const rateInput = $('#exchange_rate');
        const rateGroup = $('#exchange_rate_group');
        const amountToGroup = $('#amount_to_group');
        const targetCurrencyDisplay = $('#target-currency-display');
        const rateNote = $('#current-rate-text');
        
        if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
            // التحقق إذا كانت USD <-> IQD فقط
            const isUsdIqd = (fromCurrency === 'USD' && toCurrency === 'IQD') || 
                            (fromCurrency === 'IQD' && toCurrency === 'USD');
            
            if (isUsdIqd) {
                // USD <-> IQD: إظهار قسم سعر الصرف
                rateGroup.slideDown(300);
                amountToGroup.slideDown(300);
                
                // تحديد بيانات سعر الصرف
                const rateKey = `${fromCurrency}_${toCurrency}`;
                let rateData = exchangeRateData[rateKey];
                
                if (rateData) {
                    const displayRate = parseFloat(rateData.rate).toFixed(4);
                    rateInput.val(displayRate);
                    
                    rateNote.html(`
                        <span class="text-warning">💰</span>
                        ${rateData.display}
                    `);
                    
                    targetCurrencyDisplay.text(toCurrency);
                } else {
                    // Fallback: استخدام exchangeRates القديم
                    let key = fromCurrency + '_' + toCurrency;
                    if (exchangeRates[key]) {
                        const rate = exchangeRates[key];
                        rateInput.val(rate.toFixed(4));
                        targetCurrencyDisplay.text(toCurrency);
                    }
                }
            } else {
                // عملات أخرى: إخفاء قسم سعر الصرف
                rateGroup.slideUp(300);
                amountToGroup.slideUp(300);
                rateInput.val('1');
                targetCurrencyDisplay.text('---');
            }
        } else {
            // نفس العملة: أخفِ سعر الصرف
            rateGroup.slideUp(300);
            amountToGroup.slideUp(300);
            rateInput.val('1');
            targetCurrencyDisplay.text('---');
        }
        
        updateAmountTo();
    }

    function updateAmountTo() {
        const fromCurrency = $('#cash-currency').val();
        const toCurrency = $('#target-currency').val();
        const amountFrom = parseFloat($('#amount_from').val()) || 0;
        const rate = parseFloat($('#exchange_rate').val()) || 1;
        
        let amountTo = '';
        if (fromCurrency && toCurrency && fromCurrency !== toCurrency) {
            // التحقق إذا كانت USD <-> IQD
            const isUsdIqd = (fromCurrency === 'USD' && toCurrency === 'IQD') || 
                            (fromCurrency === 'IQD' && toCurrency === 'USD');
            
            if (isUsdIqd) {
                // استخدام السعر المدخل مباشرة
                if (fromCurrency === 'USD' && toCurrency === 'IQD') {
                    amountTo = amountFrom * rate;
                } else {
                    amountTo = amountFrom / rate;
                }
            } else {
                // للعملات الأخرى، استخدام exchangeRates
                let key = fromCurrency + '_' + toCurrency;
                if (exchangeRates[key]) {
                    amountTo = amountFrom * parseFloat(exchangeRates[key]);
                } else {
                    amountTo = amountFrom;
                }
            }
        } else {
            amountTo = amountFrom;
        }
        
        $('#amount_to').val(amountTo ? amountTo.toFixed(6) : '');
        
        // Validar monto
        validateAmount();
    }
    
    // تحديث عرض الرصيد
    function updateBalanceDisplay(currency) {
        const fromVal = $('#from-account').val();
        const account = cashAccounts.find(a => a.id == fromVal);
        currency = currency || $('#cash-currency').val();
        
        $('#current-balance-info').remove();
        $('#balance-error-alert').remove();
        
        if (account && currency) {
            // جلب الرصيد بالعملة المختارة
            $.ajax({
                url: '/api/accounts/' + fromVal + '/balance',
                method: 'GET',
                data: { currency: currency },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const balanceInfo = $('<div id="current-balance-info" class="alert alert-info mt-2">' + 
                        '<i class="fas fa-info-circle"></i> ' +
                        'الرصيد الحالي: <strong>' + parseFloat(response.balance).toFixed(2) + ' ' + currency + '</strong>' +
                        '</div>');
                    $('#amount_from').closest('.amount-section-body').find('#current-balance-info').remove();
                    $('#amount_from').closest('.amount-section-body').append(balanceInfo);
                },
                error: function() {
                    $('#balance-error-alert').remove();
                    const errorAlert = $('<div id="balance-error-alert" class="alert alert-warning mt-3">' + 
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        'خطأ في تحميل رصيد الحساب. يرجى المحاولة مرة أخرى.' + 
                        '</div>');
                    $('#amount_from').closest('.amount-section-body').append(errorAlert);
                }
            });
        }
    }
    
    // التحقق من الرصيد
    function validateAmount() {
        const fromVal = $('#from-account').val();
        const currency = $('#cash-currency').val();
        const amount = parseFloat($('#amount_from').val()) || 0;
        
        if (!fromVal || !currency || amount <= 0) {
            $('#insufficient-balance-alert').remove();
            return;
        }
        
        // جلب الرصيد والتحقق
        $.ajax({
            url: '/api/accounts/' + fromVal + '/balance',
            method: 'GET',
            data: { currency: currency },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                const balance = parseFloat(response.balance);
                if (amount > balance) {
                    $('#insufficient-balance-alert').remove();
                    const alert = $('<div id="insufficient-balance-alert" class="alert alert-danger mt-3">' + 
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        '<strong>تنبيه:</strong> المبلغ المطلوب تحويله (' + amount.toFixed(2) + ' ' + currency + ') ' +
                        'يتجاوز الرصيد المتاح في الصندوق (' + balance.toFixed(2) + ' ' + currency + ').' + 
                        '</div>');
                    $('#amount_from').closest('.amount-section-body').append(alert);
                    $('#save-btn').prop('disabled', true);
                } else {
                    $('#insufficient-balance-alert').remove();
                    if ($('#from-account').val() && $('#to-account').val() && 
                        $('#from-account').val() !== $('#to-account').val() && amount > 0 &&
                        $('#cash-currency').val() && $('#target-currency').val()) {
                        $('#save-btn').prop('disabled', false);
                    }
                }
            }
        });
    }
    
    // ربط الأحداث
    $('#from-account, #to-account').on('change', function(){ 
        filterTargetAccounts();
    });
    
    $('#cash-currency, #target-currency').on('change', function() {
        updateExchangeRate();
        updateAmountTo();
        if ($(this).attr('id') === 'cash-currency') {
            updateBalanceDisplay($(this).val());
        }
    });
    
    $('#amount_from').on('input', function() {
        updateAmountTo();
    });
    
    $('#exchange_rate').on('input', function() {
        updateAmountTo();
    });
    
    // تنفيذ أولي
    filterTargetAccounts();
    updateAmountTo();
});
</script>
@endpush
