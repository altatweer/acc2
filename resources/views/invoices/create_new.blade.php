@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1><i class="fas fa-file-invoice-dollar mr-2"></i>إنشاء فاتورة</h1>
                <p class="text-muted">إنشاء فاتورة جديدة مع دعم العملات المتعددة</p>
            </div>
            <div class="col-sm-4 text-left">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
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

        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
            @csrf
            <!-- Invoice Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>معلومات الفاتورة
                    </h3>
                    <p class="text-muted mb-0 mt-1">قم بملء المعلومات الأساسية للفاتورة</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="invoice_number" class="font-weight-bold">
                                <i class="fas fa-hashtag mr-1 text-secondary"></i>رقم الفاتورة (اختياري)
                            </label>
                            <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ old('invoice_number') }}" placeholder="سيتم إنشاؤه تلقائياً">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="customer_id" class="font-weight-bold">
                                <i class="fas fa-user mr-1 text-info"></i>العميل <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" class="form-control select2-customer" required>
                                <option value="">-- اختر العميل --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="date" class="font-weight-bold">
                                <i class="fas fa-calendar-alt mr-1 text-success"></i>التاريخ <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="currency" class="font-weight-bold">
                                <i class="fas fa-coins mr-1 text-warning"></i>العملة <span class="text-danger">*</span>
                            </label>
                            <select name="currency" id="currency" class="form-control select2-currency" required>
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->code }}" {{ old('currency') == $curr->code ? 'selected' : '' }}>
                                        {{ $curr->code }} - {{ $curr->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="exchange_rate_display" class="font-weight-bold">
                                <i class="fas fa-exchange-alt mr-1 text-primary"></i>سعر الصرف
                            </label>
                            <input type="text" id="exchange_rate_display" class="form-control" disabled>
                            <input type="hidden" name="exchange_rate" id="exchange_rate">
                            <small class="form-text text-muted">تلقائي</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Total Display -->
            <div class="card shadow mb-4">
                <div class="card-body text-center bg-light">
                    <h5 class="mb-2">إجمالي الفاتورة</h5>
                    <h3 class="mb-0" id="invoice_total_display">
                        <span id="total_amount">0.00</span>
                        <span id="total_currency"></span>
                    </h3>
                    <input type="hidden" name="total" id="invoice_total" value="0.00">
                </div>
            </div>

            <!-- Invoice Items Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-list mr-2 text-primary"></i>بنود الفاتورة
                            </h3>
                            <p class="text-muted mb-0 mt-1">أضف بنود الفاتورة (حد أدنى بند واحد)</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="addItem" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i>إضافة عنصر
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="itemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 35%">البند</th>
                                    <th style="width: 12%">الكمية</th>
                                    <th style="width: 18%">السعر الفردي</th>
                                    <th style="width: 18%">الإجمالي</th>
                                    <th style="width: 12%">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-item-index="0">
                                    <td class="text-center">1</td>
                                    <td>
                                        <select name="items[0][item_id]" class="form-control select2-item" data-index="0" required>
                                            <option value="">-- اختر الصنف --</option>
                                            @foreach($items as $itm)
                                                <option value="{{ $itm->id }}" data-price="{{ $itm->unit_price }}">
                                                    {{ $itm->name }} ({{ $itm->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control item-quantity text-center" step="1" min="1" placeholder="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_price]" class="form-control item-price text-right" step="0.01" min="0" placeholder="0.00" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][line_total]" class="form-control item-total text-right bg-light font-weight-bold" step="0.01" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-item" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(\App\Models\Setting::get('enable_invoice_expense_attachment', false))
            <!-- Expense Attachment Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-receipt mr-2 text-primary"></i>ملحق المصاريف
                            </h3>
                            <p class="text-muted mb-0 mt-1">إضافة مصاريف مرتبطة بالفاتورة</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="addExpense" class="btn btn-success btn-sm">
                                <i class="fas fa-plus-circle mr-1"></i>إضافة مصروف
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="expenseTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 22%">حساب النقد</th>
                                    <th style="width: 22%">حساب المصروف</th>
                                    <th style="width: 12%">المبلغ</th>
                                    <th style="width: 10%">العملة</th>
                                    <th style="width: 19%">وصف المصروف</th>
                                    <th style="width: 10%">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-expense-index="0">
                                    <td class="text-center">1</td>
                                    <td>
                                        <select name="expense_attachment_lines[0][cash_account_id]" class="form-control select2-cash-account" data-index="0">
                                            <option value="">-- اختر الحساب --</option>
                                            @foreach(\App\Models\Account::where('is_cash_box', 1)->get() as $acc)
                                                <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ $acc->name }}">
                                                    {{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="expense_attachment_lines[0][expense_account_id]" class="form-control select2-expense-account" data-index="0">
                                            <option value="">-- اختر الحساب --</option>
                                            @foreach(\App\Models\Account::where('is_group', 0)->where('is_cash_box', 0)->get() as $acc)
                                                <option value="{{ $acc->id }}" data-code="{{ $acc->code }}" data-name="{{ $acc->name }}">
                                                    {{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="expense_attachment_lines[0][amount]" class="form-control text-right" step="0.01" min="0" placeholder="0.00">
                                    </td>
                                    <td>
                                        <select name="expense_attachment_lines[0][currency]" class="form-control select2-currency-small">
                                            @foreach($currencies as $curr)
                                                <option value="{{ $curr->code }}" {{ $curr->code == old('currency', 'IQD') ? 'selected' : '' }}>
                                                    {{ $curr->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="expense_attachment_lines[0][description]" class="form-control" placeholder="وصف المصروف">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-expense" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="card shadow">
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-save mr-1"></i>حفظ الفاتورة
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>إلغاء
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

.form-control.text-center {
    text-align: center;
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
}
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

<script>
let itemIndex = 1;
let expenseIndex = 1;

// بيانات العناصر والحسابات
@php
$itemsData = $items->map(function($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'type' => $item->type,
        'price' => $item->unit_price
    ];
})->values();

$cashAccountsData = \App\Models\Account::where('is_cash_box', 1)->get()->map(function($acc) {
    return [
        'id' => $acc->id,
        'code' => $acc->code ?? '',
        'name' => $acc->name
    ];
})->values();

$expenseAccountsData = \App\Models\Account::where('is_group', 0)->where('is_cash_box', 0)->get()->map(function($acc) {
    return [
        'id' => $acc->id,
        'code' => $acc->code ?? '',
        'name' => $acc->name
    ];
})->values();

$currenciesData = $currencies->map(function($curr) {
    return [
        'code' => $curr->code,
        'name' => $curr->name
    ];
})->values();
@endphp

const itemsData = @json($itemsData);
const cashAccountsData = @json($cashAccountsData);
const expenseAccountsData = @json($expenseAccountsData);
const currenciesData = @json($currenciesData);

$(document).ready(function(){
    
    // تنسيق عرض الحسابات في Select2 - نسخ من القيود بالضبط
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

    // تنسيق عرض الحساب المختار - نسخ من القيود بالضبط
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

    // تهيئة Select2 للعملة الرئيسية
    $('#currency').select2({
        theme: 'bootstrap4',
        width: '100%',
        closeOnSelect: true
    });

    // تهيئة Select2 للعميل
    $('.select2-customer').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'ابحث عن العميل...',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });

    // تهيئة Select2 للعناصر
    $('.select2-item').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'ابحث عن الصنف...',
        language: {
            noResults: function() {
                return "لا توجد نتائج";
            },
            searching: function() {
                return "جاري البحث...";
            }
        }
    });

    // تهيئة Select2 للحسابات - نسخ من القيود بالضبط
    function initializeAccountSelect2() {
        $('.select2-cash-account, .select2-expense-account').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('body'),
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

    // تهيئة Select2 للعملة الصغيرة
    $('.select2-currency-small').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('body'),
        closeOnSelect: true,
        minimumResultsForSearch: Infinity
    });

    // تهيئة Select2 للحسابات الموجودة
    initializeAccountSelect2();

    // تحديث سعر الصرف
    const rates = @json($currencies->pluck('exchange_rate', 'code'));
    
    $('#currency').on('change', function(){
        const code = $(this).val();
        const rate = parseFloat(rates[code]) || 1;
        $('#exchange_rate_display').val(rate.toFixed(6));
        $('#exchange_rate').val(rate);
        $('#total_currency').text(code);
    });
    
    // تحديث سعر الصرف عند التحميل
    if ($('#currency').val()) {
        const code = $('#currency').val();
        const rate = parseFloat(rates[code]) || 1;
        $('#exchange_rate_display').val(rate.toFixed(6));
        $('#exchange_rate').val(rate);
        $('#total_currency').text(code);
    }

    // تحديث إجمالي الفاتورة
    function updateInvoiceTotal(){
        let total = 0;
        $('.item-total').each(function(){ 
            total += parseFloat($(this).val()) || 0; 
        });
        total = parseFloat(total.toFixed(2));
        $('#invoice_total').val(total);
        $('#total_amount').text(total.toLocaleString('ar-EG', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    // تحديث إجمالي السطر
    function updateItemTotal($row){
        let q = parseFloat($row.find('.item-quantity').val()) || 0;
        let p = parseFloat($row.find('.item-price').val()) || 0;
        $row.find('.item-total').val((q * p).toFixed(2));
        updateInvoiceTotal();
    }

    // عند اختيار عنصر، تحديث السعر
    $(document).on('change', '.select2-item', function(){
        let price = parseFloat($(this).find('option:selected').data('price')) || 0;
        $(this).closest('tr').find('.item-price').val(price.toFixed(2));
        updateItemTotal($(this).closest('tr'));
    });

    // عند تغيير الكمية أو السعر
    $(document).on('input', '.item-quantity, .item-price', function(){
        updateItemTotal($(this).closest('tr'));
    });

    // إضافة عنصر جديد
    function addNewItem() {
        let optionsHtml = '<option value="">-- اختر الصنف --</option>';
        itemsData.forEach(function(item) {
            optionsHtml += `<option value="${item.id}" data-price="${item.price}">${item.name} (${item.type})</option>`;
        });
        
        let row = `
        <tr data-item-index="${itemIndex}">
            <td class="text-center">${itemIndex + 1}</td>
            <td>
                <select name="items[${itemIndex}][item_id]" class="form-control select2-item" data-index="${itemIndex}" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity text-center" step="1" min="1" placeholder="1" value="1" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price text-right" step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][line_total]" class="form-control item-total text-right bg-light font-weight-bold" step="0.01" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#itemsTable tbody').append(row);
        
        // تهيئة Select2 للعنصر الجديد
        $(`select[name="items[${itemIndex}][item_id]"]`).select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'ابحث عن الصنف...',
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            }
        });
        
        itemIndex++;
        updateRemoveItemButtons();
    }

    // حذف عنصر
    $(document).on('click', '.remove-item', function() {
        if ($('#itemsTable tbody tr').length > 1) {
            $(this).closest('tr').find('.select2-item').select2('destroy');
            $(this).closest('tr').remove();
            updateItemNumbers();
            updateRemoveItemButtons();
            updateInvoiceTotal();
        }
    });

    // تحديث أرقام العناصر
    function updateItemNumbers() {
        $('#itemsTable tbody tr').each(function(index){
            $(this).find('td:first').text(index + 1);
        });
    }

    // تحديث أزرار حذف العناصر
    function updateRemoveItemButtons() {
        let rowCount = $('#itemsTable tbody tr').length;
        $('.remove-item').prop('disabled', rowCount <= 1);
    }

    // ربط زر إضافة عنصر
    $(document).on('click', '#addItem', function(e) {
        e.preventDefault();
        addNewItem();
    });

    @if(\App\Models\Setting::get('enable_invoice_expense_attachment', false))
    // إضافة مصروف جديد
    function addNewExpense() {
        let cashOptionsHtml = '<option value="">-- اختر الحساب --</option>';
        cashAccountsData.forEach(function(acc) {
            let displayText = acc.code ? acc.code + ' - ' + acc.name : acc.name;
            cashOptionsHtml += `<option value="${acc.id}" data-code="${acc.code || ''}" data-name="${acc.name}">${displayText}</option>`;
        });
        
        let expenseOptionsHtml = '<option value="">-- اختر الحساب --</option>';
        expenseAccountsData.forEach(function(acc) {
            let displayText = acc.code ? acc.code + ' - ' + acc.name : acc.name;
            expenseOptionsHtml += `<option value="${acc.id}" data-code="${acc.code || ''}" data-name="${acc.name}">${displayText}</option>`;
        });
        
        let currencyOptionsHtml = '';
        currenciesData.forEach(function(curr) {
            let selected = curr.code === 'IQD' ? 'selected' : '';
            currencyOptionsHtml += `<option value="${curr.code}" ${selected}>${curr.code}</option>`;
        });
        
        let row = `
        <tr data-expense-index="${expenseIndex}">
            <td class="text-center">${expenseIndex + 1}</td>
            <td>
                <select name="expense_attachment_lines[${expenseIndex}][cash_account_id]" class="form-control select2-cash-account" data-index="${expenseIndex}">
                    ${cashOptionsHtml}
                </select>
            </td>
            <td>
                <select name="expense_attachment_lines[${expenseIndex}][expense_account_id]" class="form-control select2-expense-account" data-index="${expenseIndex}">
                    ${expenseOptionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="expense_attachment_lines[${expenseIndex}][amount]" class="form-control text-right" step="0.01" min="0" placeholder="0.00">
            </td>
            <td>
                <select name="expense_attachment_lines[${expenseIndex}][currency]" class="form-control select2-currency-small">
                    ${currencyOptionsHtml}
                </select>
            </td>
            <td>
                <input type="text" name="expense_attachment_lines[${expenseIndex}][description]" class="form-control" placeholder="وصف المصروف">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-expense">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#expenseTable tbody').append(row);
        
        // تهيئة Select2 للحسابات الجديدة
        $(`select[name="expense_attachment_lines[${expenseIndex}][cash_account_id]"], select[name="expense_attachment_lines[${expenseIndex}][expense_account_id]"]`).select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('body'),
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

        // تهيئة Select2 للعملة الصغيرة
        $(`select[name="expense_attachment_lines[${expenseIndex}][currency]"]`).select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('body'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity
        });
        
        expenseIndex++;
        updateRemoveExpenseButtons();
    }

    // حذف مصروف
    $(document).on('click', '.remove-expense', function() {
        // السماح بحذف جميع الصفوف إذا أراد المستخدم
        $(this).closest('tr').find('.select2-cash-account, .select2-expense-account, .select2-currency-small').select2('destroy');
        $(this).closest('tr').remove();
        updateExpenseNumbers();
        updateRemoveExpenseButtons();
        
        // إذا لم يبق أي صفوف، إضافة صف فارغ جديد
        if ($('#expenseTable tbody tr').length === 0) {
            addNewExpense();
        }
    });

    // تحديث أرقام المصاريف
    function updateExpenseNumbers() {
        $('#expenseTable tbody tr').each(function(index){
            $(this).find('td:first').text(index + 1);
        });
    }

    // تحديث أزرار حذف المصاريف
    function updateRemoveExpenseButtons() {
        // السماح بحذف جميع الصفوف - ملحق المصاريف اختياري
        $('.remove-expense').prop('disabled', false);
    }

    // ربط زر إضافة مصروف
    $(document).on('click', '#addExpense', function(e) {
        e.preventDefault();
        addNewExpense();
    });
    @endif

    // التحقق من الفاتورة قبل الإرسال
    $('#invoiceForm').on('submit', function(e) {
        // فحص وجود بنود
        if($('#itemsTable tbody tr').length === 0 || parseFloat($('#invoice_total').val()) <= 0) {
            e.preventDefault();
            alert('⚠️ يجب إضافة بند واحد على الأقل للفاتورة');
            return false;
        }

        // فحص اختيار العناصر
        let missingItems = false;
        $('.select2-item').each(function() {
            if (!$(this).val()) {
                missingItems = true;
                $(this).next('.select2-container').css('border', '2px solid #dc3545');
            } else {
                $(this).next('.select2-container').css('border', '');
            }
        });
        
        if (missingItems) {
            e.preventDefault();
            alert('⚠️ يجب اختيار صنف لجميع البنود');
            return false;
        }

        @if(\App\Models\Setting::get('enable_invoice_expense_attachment', false))
        // فحص اختيار الحسابات في المصاريف (فقط للصفوف المملوءة)
        let missingAccounts = false;
        $('#expenseTable tbody tr').each(function() {
            let $row = $(this);
            let cashAccount = $row.find('.select2-cash-account').val();
            let expenseAccount = $row.find('.select2-expense-account').val();
            let amount = parseFloat($row.find('input[name*="[amount]"]').val()) || 0;
            
            // إذا كان هناك أي بيانات في الصف، يجب ملء كل شيء
            if (cashAccount || expenseAccount || amount > 0) {
                if (!cashAccount || !expenseAccount || amount <= 0) {
                    missingAccounts = true;
                    if (!cashAccount) {
                        $row.find('.select2-cash-account').next('.select2-container').css('border', '2px solid #dc3545');
                    } else {
                        $row.find('.select2-cash-account').next('.select2-container').css('border', '');
                    }
                    if (!expenseAccount) {
                        $row.find('.select2-expense-account').next('.select2-container').css('border', '2px solid #dc3545');
                    } else {
                        $row.find('.select2-expense-account').next('.select2-container').css('border', '');
                    }
                } else {
                    $row.find('.select2-cash-account, .select2-expense-account').next('.select2-container').css('border', '');
                }
            } else {
                // إذا كان الصف فارغ تماماً، لا مشكلة
                $row.find('.select2-cash-account, .select2-expense-account').next('.select2-container').css('border', '');
            }
        });
        
        if (missingAccounts) {
            e.preventDefault();
            alert('⚠️ إذا أضفت مصروف، يجب ملء جميع الحقول (حساب النقد، حساب المصروف، والمبلغ)');
            return false;
        }
        @endif
        
        // إظهار loading
        $('#submitBtn').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-1"></i>جاري الحفظ...');
    });
    
    // تهيئة أولى
    updateInvoiceTotal();
    updateRemoveItemButtons();
    @if(\App\Models\Setting::get('enable_invoice_expense_attachment', false))
    updateRemoveExpenseButtons();
    @endif
});
</script>
@endpush

