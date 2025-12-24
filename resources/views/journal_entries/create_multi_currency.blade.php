@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1><i class="fas fa-globe mr-2"></i>@lang('messages.add_multi_currency_entry')</h1>
                <p class="text-muted">إنشاء قيد محاسبي متعدد العملات مع دعم التحويل بين العملات</p>
            </div>
            <div class="col-sm-4 text-left">
                <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> العودة
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">أخطاء التحقق</h5>
                        <ul class="mb-0 list-unstyled">
                            @foreach ($errors->all() as $error)
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
            @csrf
            <!-- Entry Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>تفاصيل القيد
                    </h3>
                    <p class="text-muted mb-0 mt-1">قم بملء المعلومات الأساسية للقيد</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="date" class="font-weight-bold">
                                <i class="fas fa-calendar-alt mr-1 text-success"></i>التاريخ
                            </label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="description" class="font-weight-bold">
                                <i class="fas fa-comment-alt mr-1 text-info"></i>الوصف
                            </label>
                            <input type="text" name="description" id="description" class="form-control" placeholder="وصف القيد" value="{{ old('description') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Journal Entry Lines Card -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-list mr-2 text-primary"></i>خطوط القيد
                            </h3>
                            <p class="text-muted mb-0 mt-1">أضف خطوط القيد المحاسبي متعددة العملات (حد أدنى سطرين)</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="addLine" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i>إضافة سطر جديد
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-lg mr-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1" style="font-weight: 600;">ملاحظات مهمة - قيد متعدد العملات</h6>
                                <p class="mb-0" style="font-size: 14px;">
                                    • يجب أن يكون مجموع المدين (بعد التحويل للعملة الأساسية) يساوي مجموع الدائن<br>
                                    • يمكنك اختيار عملة مختلفة لكل سطر<br>
                                    • سعر الصرف يستخدم لتحويل المبلغ للعملة الأساسية (IQD) للتوازن<br>
                                    • يمكنك البحث عن الحسابات باستخدام الكود أو الاسم<br>
                                    • الحد الأدنى سطرين والحد الأقصى غير محدود
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="linesTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%">الحساب</th>
                                    <th style="width: 15%">الوصف</th>
                                    <th style="width: 10%">مدين</th>
                                    <th style="width: 10%">دائن</th>
                                    <th style="width: 10%">العملة</th>
                                    <th style="width: 10%">سعر الصرف</th>
                                    <th style="width: 10%">المبلغ المحول (IQD)</th>
                                    <th style="width: 15%">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-line-index="0">
                                    <td>
                                        <select name="lines[0][account_id]" class="form-control select2-account account-select" data-index="0" required>
                                            <option value="">-- اختر الحساب --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ $acc->name }}" data-currency="{{ $acc->default_currency ?? 'IQD' }}">
                                                    {{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="lines[0][description]" class="form-control" placeholder="وصف العملية" value="{{ old('lines.0.description') }}">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[0][debit]" class="form-control debit text-right" step="0.01" min="0" placeholder="0.00" value="{{ old('lines.0.debit', 0) }}" data-index="0">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[0][credit]" class="form-control credit text-right" step="0.01" min="0" placeholder="0.00" value="{{ old('lines.0.credit', 0) }}" data-index="0">
                                    </td>
                                    <td>
                                        <select name="lines[0][currency]" class="form-control currency-select" data-index="0" required>
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->code }}" {{ old('lines.0.currency', 'IQD') == $curr->code ? 'selected' : '' }}>
                                                    {{ $curr->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="lines[0][exchange_rate]" class="form-control exchange-rate text-right" step="0.000001" min="0.000001" placeholder="1.000000" value="{{ old('lines.0.exchange_rate', 1) }}" required data-index="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control converted-amount text-right" value="0.00" readonly data-index="0" style="background-color: #f8f9fa; font-weight: 600;">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-line" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr data-line-index="1">
                                    <td>
                                        <select name="lines[1][account_id]" class="form-control select2-account account-select" data-index="1" required>
                                            <option value="">-- اختر الحساب --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ $acc->name }}" data-currency="{{ $acc->default_currency ?? 'IQD' }}">
                                                    {{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="lines[1][description]" class="form-control" placeholder="وصف العملية" value="{{ old('lines.1.description') }}">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[1][debit]" class="form-control debit text-right" step="0.01" min="0" placeholder="0.00" value="{{ old('lines.1.debit', 0) }}" data-index="1">
                                    </td>
                                    <td>
                                        <input type="number" name="lines[1][credit]" class="form-control credit text-right" step="0.01" min="0" placeholder="0.00" value="{{ old('lines.1.credit', 0) }}" data-index="1">
                                    </td>
                                    <td>
                                        <select name="lines[1][currency]" class="form-control currency-select" data-index="1" required>
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->code }}" {{ old('lines.1.currency', 'IQD') == $curr->code ? 'selected' : '' }}>
                                                    {{ $curr->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="lines[1][exchange_rate]" class="form-control exchange-rate text-right" step="0.000001" min="0.000001" placeholder="1.000000" value="{{ old('lines.1.exchange_rate', 1) }}" required data-index="1">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control converted-amount text-right" value="0.00" readonly data-index="1" style="background-color: #f8f9fa; font-weight: 600;">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-line">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <th colspan="2" class="text-right font-weight-bold">الإجمالي (بعد التحويل للعملة الأساسية):</th>
                                    <th id="totalDebit" class="text-center font-weight-bold">0.00</th>
                                    <th id="totalCredit" class="text-center font-weight-bold">0.00</th>
                                    <th colspan="2"></th>
                                    <th id="totalConverted" class="text-center font-weight-bold">0.00</th>
                                    <th id="difference" class="text-center font-weight-bold">0.00</th>
                                </tr>
                                <tr id="balanceStatus" style="display: none;">
                                    <td colspan="8" class="text-center">
                                        <span id="balanceMessage"></span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" id="addLineFooter" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>إضافة سطر جديد
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-save mr-1"></i>حفظ القيد
                            </button>
                            <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>العودة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />

<style>
/* Modern Card Styling */
.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    border-bottom: 2px solid #f0f0f0;
    padding: 1.25rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
}

/* Select2 Modern Styling */
.select2-container--bootstrap4 .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection--single {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 0;
    color: #495057;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 8px;
}

/* Select2 Dropdown */
.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 8px;
}

.select2-search--dropdown .select2-search__field:focus {
    border-color: #80bdff;
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select2-results__option {
    padding: 10px;
}

.select2-results__option--highlighted {
    background-color: #007bff;
    color: white;
}

.select2-results__option[aria-selected="true"] {
    background-color: #e9ecef;
}

/* Account Option Styling */
.select2-results__option .account-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.select2-results__option .account-name {
    font-weight: 500;
    color: #212529;
}

.select2-results__option .account-code {
    font-size: 0.85rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.select2-results__option--highlighted .account-code {
    background: rgba(255,255,255,0.3);
    color: rgba(255,255,255,0.9);
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}

.table tbody td {
    vertical-align: middle;
}

.table tfoot th {
    background-color: #e9ecef;
    font-weight: 600;
}

.table tfoot .table-info {
    background-color: #d1ecf1;
}

/* Balance Status */
#difference.balanced {
    color: #155724;
    background-color: #d4edda;
}

#difference.unbalanced {
    color: #721c24;
    background-color: #f8d7da;
}

#balanceStatus .alert {
    margin-bottom: 0;
    padding: 0.5rem 1rem;
}

/* Form Controls */
.form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control.text-right {
    text-align: right;
    font-weight: 500;
}

.exchange-rate {
    background-color: #fff3cd !important;
    border-color: #ffc107 !important;
}

.converted-amount {
    background-color: #d1ecf1 !important;
    font-weight: 600;
}

/* Buttons */
.btn {
    border-radius: 4px;
    font-weight: 500;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .card-header .row {
        flex-direction: column;
    }
    
    .card-header .col-auto {
        margin-top: 1rem;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

<script>
let lineIndex = 2;
const defaultCurrency = 'IQD';

$(document).ready(function(){
    // تنسيق عرض الحسابات في Select2
    function formatAccountOption(account) {
        if (!account.id) return account.text;
        
        const $option = $(account.element);
        const code = $option.data('code') || '';
        const name = $option.data('name') || account.text;
        
        if (code) {
            return $(`
                <div class="account-option">
                    <span class="account-name">${name}</span>
                    <span class="account-code">${code}</span>
                </div>
            `);
        }
        
        return account.text;
    }

    // تنسيق عرض الحساب المختار
    function formatAccountSelection(account) {
        if (!account.id) return account.text;
        
        const $option = $(account.element);
        const code = $option.data('code') || '';
        const name = $option.data('name') || account.text;
        
        if (code) {
            return `${code} - ${name}`;
        }
        
        return account.text;
    }

    // تهيئة Select2 للحسابات
    function initializeSelect2() {
        $('.select2-account').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'ابحث عن الحساب...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            },
            templateResult: formatAccountOption,
            templateSelection: formatAccountSelection,
            escapeMarkup: function(markup) { return markup; }
        });
    }

    // حساب المبلغ المحول للعملة الأساسية
    function updateConvertedAmount(index) {
        const debit = parseFloat($(`input.debit[data-index="${index}"]`).val()) || 0;
        const credit = parseFloat($(`input.credit[data-index="${index}"]`).val()) || 0;
        const amount = debit > 0 ? debit : credit;
        const currency = $(`select.currency-select[data-index="${index}"]`).val();
        const exchangeRate = parseFloat($(`input.exchange-rate[data-index="${index}"]`).val()) || 1;
        
        let convertedAmount = 0;
        if (amount > 0) {
            if (currency === defaultCurrency) {
                convertedAmount = amount;
            } else {
                convertedAmount = amount * exchangeRate;
            }
        }
        
        $(`input.converted-amount[data-index="${index}"]`).val(convertedAmount.toFixed(2));
    }

    // تحديث الإجماليات (بعد التحويل للعملة الأساسية)
    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        let totalConverted = 0;
        
        $('tbody tr').each(function() {
            const index = $(this).data('line-index');
            const debit = parseFloat($(`input.debit[data-index="${index}"]`).val()) || 0;
            const credit = parseFloat($(`input.credit[data-index="${index}"]`).val()) || 0;
            const converted = parseFloat($(`input.converted-amount[data-index="${index}"]`).val()) || 0;
            
            totalDebit += debit;
            totalCredit += credit;
            totalConverted += converted;
        });
        
        // حساب الإجماليات بعد التحويل للعملة الأساسية
        let totalDebitConverted = 0;
        let totalCreditConverted = 0;
        
        $('tbody tr').each(function() {
            const index = $(this).data('line-index');
            const debit = parseFloat($(`input.debit[data-index="${index}"]`).val()) || 0;
            const credit = parseFloat($(`input.credit[data-index="${index}"]`).val()) || 0;
            const currency = $(`select.currency-select[data-index="${index}"]`).val();
            const exchangeRate = parseFloat($(`input.exchange-rate[data-index="${index}"]`).val()) || 1;
            
            if (debit > 0) {
                if (currency === defaultCurrency) {
                    totalDebitConverted += debit;
                } else {
                    totalDebitConverted += debit * exchangeRate;
                }
            }
            
            if (credit > 0) {
                if (currency === defaultCurrency) {
                    totalCreditConverted += credit;
                } else {
                    totalCreditConverted += credit * exchangeRate;
                }
            }
        });
        
        let difference = Math.abs(totalDebitConverted - totalCreditConverted);
        
        $('#totalDebit').text(totalDebit.toFixed(2));
        $('#totalCredit').text(totalCredit.toFixed(2));
        $('#totalConverted').text(totalConverted.toFixed(2));
        $('#difference').text(difference.toFixed(2));
        
        // تحديث حالة التوازن
        const $balanceStatus = $('#balanceStatus');
        const $balanceMessage = $('#balanceMessage');
        
        if (difference < 0.01 && (totalDebitConverted > 0 || totalCreditConverted > 0)) {
            $('#difference').removeClass('unbalanced').addClass('balanced');
            $balanceStatus.show().removeClass('table-danger').addClass('table-success');
            $balanceMessage.html('<i class="fas fa-check-circle mr-2"></i><strong>القيد متوازن</strong> - جاهز للحفظ (بعد التحويل للعملة الأساسية)');
        } else if (totalDebitConverted > 0 || totalCreditConverted > 0) {
            $('#difference').removeClass('balanced').addClass('unbalanced');
            $balanceStatus.show().removeClass('table-success').addClass('table-danger');
            $balanceMessage.html(`<i class="fas fa-exclamation-triangle mr-2"></i><strong>القيد غير متوازن</strong> - الفرق: ${difference.toFixed(2)} (بعد التحويل للعملة الأساسية)`);
        } else {
            $balanceStatus.hide();
            $('#difference').removeClass('balanced unbalanced');
        }
    }
    
    // تحديث العملة الافتراضية عند اختيار الحساب
    $(document).on('change', '.account-select', function() {
        const index = $(this).data('index');
        const selectedOption = $(this).find('option:selected');
        const accountCurrency = selectedOption.data('currency') || defaultCurrency;
        
        // تعيين العملة الافتراضية للحساب
        $(`select.currency-select[data-index="${index}"]`).val(accountCurrency).trigger('change');
    });
    
    // إضافة سطر جديد
    function addNewLine() {
        let row = `
        <tr data-line-index="${lineIndex}">
            <td>
                <select name="lines[${lineIndex}][account_id]" class="form-control select2-account account-select" data-index="${lineIndex}" required>
                    <option value="">-- اختر الحساب --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ $acc->name }}" data-currency="{{ $acc->default_currency ?? 'IQD' }}">
                            {{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="lines[${lineIndex}][description]" class="form-control" placeholder="وصف العملية">
            </td>
            <td>
                <input type="number" name="lines[${lineIndex}][debit]" class="form-control debit text-right" step="0.01" min="0" placeholder="0.00" value="0" data-index="${lineIndex}">
            </td>
            <td>
                <input type="number" name="lines[${lineIndex}][credit]" class="form-control credit text-right" step="0.01" min="0" placeholder="0.00" value="0" data-index="${lineIndex}">
            </td>
            <td>
                <select name="lines[${lineIndex}][currency]" class="form-control currency-select" data-index="${lineIndex}" required>
                    @foreach($currencies as $curr)
                        <option value="{{ $curr->code }}">{{ $curr->code }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="lines[${lineIndex}][exchange_rate]" class="form-control exchange-rate text-right" step="0.000001" min="0.000001" placeholder="1.000000" value="1" required data-index="${lineIndex}">
            </td>
            <td>
                <input type="text" class="form-control converted-amount text-right" value="0.00" readonly data-index="${lineIndex}" style="background-color: #f8f9fa; font-weight: 600;">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-line">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#linesTable tbody').append(row);
        
        // تهيئة Select2 للسطر الجديد
        $(`select[name="lines[${lineIndex}][account_id]"]`).select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'ابحث عن الحساب...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            },
            templateResult: formatAccountOption,
            templateSelection: formatAccountSelection,
            escapeMarkup: function(markup) { return markup; }
        });
        
        lineIndex++;
        updateRemoveButtons();
        updateTotals();
    }
    
    // ربط أحداث إضافة السطر
    $('#addLine, #addLineFooter').on('click', addNewLine);
    
    // حذف سطر
    $(document).on('click', '.remove-line', function() {
        if ($('#linesTable tbody tr').length > 2) {
            $(this).closest('tr').find('.select2-account').select2('destroy');
            $(this).closest('tr').remove();
            updateRemoveButtons();
            updateTotals();
        }
    });
    
    // تحديث أزرار الحذف
    function updateRemoveButtons() {
        let rowCount = $('#linesTable tbody tr').length;
        $('.remove-line').prop('disabled', rowCount <= 2);
    }
    
    // تحديث المبلغ المحول عند تغيير أي قيمة
    $(document).on('input change', '.debit, .credit, .currency-select, .exchange-rate', function() {
        const index = $(this).data('index');
        updateConvertedAmount(index);
        updateTotals();
    });
    
    // تهيئة Select2 للحسابات الموجودة
    initializeSelect2();
    
    // التحقق من القيد قبل الإرسال
    $('#journalForm').on('submit', function(e) {
        let totalDebitConverted = 0;
        let totalCreditConverted = 0;
        
        $('tbody tr').each(function() {
            const index = $(this).data('line-index');
            const debit = parseFloat($(`input.debit[data-index="${index}"]`).val()) || 0;
            const credit = parseFloat($(`input.credit[data-index="${index}"]`).val()) || 0;
            const currency = $(`select.currency-select[data-index="${index}"]`).val();
            const exchangeRate = parseFloat($(`input.exchange-rate[data-index="${index}"]`).val()) || 1;
            
            if (debit > 0) {
                if (currency === defaultCurrency) {
                    totalDebitConverted += debit;
                } else {
                    totalDebitConverted += debit * exchangeRate;
                }
            }
            
            if (credit > 0) {
                if (currency === defaultCurrency) {
                    totalCreditConverted += credit;
                } else {
                    totalCreditConverted += credit * exchangeRate;
                }
            }
        });
        
        let difference = Math.abs(totalDebitConverted - totalCreditConverted);
        
        // فحص التوازن
        if (difference > 0.01) {
            e.preventDefault();
            alert(`❌ القيد غير متوازن بعد التحويل للعملة الأساسية!\n\nالمدين (محول): ${totalDebitConverted.toFixed(2)}\nالدائن (محول): ${totalCreditConverted.toFixed(2)}\nالفرق: ${difference.toFixed(2)}\n\nيجب أن يكون مجموع المدين يساوي مجموع الدائن بعد التحويل`);
            return false;
        }
        
        // فحص وجود مبالغ
        if (totalDebitConverted === 0 && totalCreditConverted === 0) {
            e.preventDefault();
            alert('⚠️ يجب إدخال مبالغ في القيد');
            return false;
        }
        
        // فحص اختيار الحسابات
        let missingAccounts = false;
        $('.select2-account').each(function() {
            if (!$(this).val()) {
                missingAccounts = true;
                $(this).next('.select2-container').css('border', '2px solid #dc3545');
            } else {
                $(this).next('.select2-container').css('border', '');
            }
        });
        
        if (missingAccounts) {
            e.preventDefault();
            alert('⚠️ يجب اختيار حساب لجميع السطور');
            return false;
        }
        
        // إظهار loading
        $('#submitBtn').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-1"></i>جاري الحفظ...');
    });
    
    // تهيئة أولى
    $('tbody tr').each(function() {
        const index = $(this).data('line-index');
        updateConvertedAmount(index);
    });
    updateTotals();
    updateRemoveButtons();
});
</script>
@endpush
