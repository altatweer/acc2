@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">دفع راتب موظف</h1>
            <a href="{{ route('salary-payments.index') }}" class="btn btn-secondary">عودة للقائمة</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="card mt-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('salary-payments.create') }}">
                        @csrf
                        <div class="form-group">
                            <label>الشهر</label>
                            <select name="salary_batch_id" id="salary_batch_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">اختر الشهر</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ $selectedBatchId == $batch->id ? 'selected' : '' }}>{{ $batch->month }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedBatchId && $employees->count() == 0)
                            <div class="alert alert-warning">لا يوجد موظفون لم يستلموا رواتبهم في هذا الشهر.</div>
                        @endif
                        @if($selectedBatchId)
                        <div class="form-group">
                            <label>الموظف</label>
                            <select name="employee_id" id="employee_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">اختر الموظف</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->employee_number }}) - {{ $emp->currency }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </form>
                    
                    @if($selectedBatchId && $selectedEmployeeId && !$salary)
                        <div class="alert alert-warning">لا يوجد راتب لهذا الموظف في هذا الشهر أو تم دفعه بالفعل.</div>
                    @endif
                    
                    @if($salary)
                        <!-- معلومات الراتب -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> تفاصيل الراتب</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الموظف:</strong> {{ $salary->employee->name }}<br>
                                    <strong>عملة الراتب:</strong> {{ $salary->employee->currency }}<br>
                                    <strong>الراتب الأساسي:</strong> {{ number_format($salary->gross_salary, 2) }} {{ $salary->employee->currency }}
                                </div>
                                <div class="col-md-6">
                                    <strong>البدلات:</strong> {{ number_format($salary->total_allowances, 2) }} {{ $salary->employee->currency }}<br>
                                    <strong>الخصومات:</strong> {{ number_format($salary->total_deductions, 2) }} {{ $salary->employee->currency }}<br>
                                    <strong>صافي الراتب:</strong> {{ number_format($salary->net_salary, 2) }} {{ $salary->employee->currency }}
                                </div>
                            </div>
                        </div>

                        <!-- نموذج السداد متعدد العملات -->
                        <form method="POST" action="{{ route('salary-payments.store') }}">
                            @csrf
                            <input type="hidden" name="salary_batch_id" value="{{ $selectedBatchId }}">
                            <input type="hidden" name="employee_id" value="{{ $selectedEmployeeId }}">
                            
                            <!-- الخطوة 1: اختيار الصندوق النقدي -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5><i class="fas fa-cash-register"></i> الخطوة 1: اختيار الصندوق النقدي</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="cash_account_id">الصندوق النقدي</label>
                                        <select name="cash_account_id" id="cash_account_id" class="form-control" required>
                                            <option value="">-- اختر الصندوق النقدي --</option>
                                            @foreach($cashAccounts as $account)
                                                <option value="{{ $account->id }}" 
                                                        data-currency="{{ $account->default_currency }}" 
                                                        data-balance="{{ $account->balance($account->default_currency) }}">
                                                    {{ $account->name }} ({{ $account->default_currency }})
                                                    - الرصيد: {{ number_format($account->balance($account->default_currency), 2) }} {{ $account->default_currency }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- الخطوة 2: اختيار عملة الدفع -->
                            <div class="card mb-3" id="currency_step" style="display: none;">
                                <div class="card-header bg-success text-white">
                                    <h5><i class="fas fa-money-bill"></i> الخطوة 2: اختيار عملة الدفع</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="payment_currency">عملة الدفع</label>
                                        <select name="payment_currency" id="payment_currency" class="form-control" required disabled>
                                            <option value="">-- اختر الصندوق أولاً --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- الخطوة 3: إدخال المبلغ وسعر الصرف -->
                            <div class="card mb-3" id="amount_step" style="display: none;">
                                <div class="card-header bg-warning text-dark">
                                    <h5><i class="fas fa-calculator"></i> الخطوة 3: تأكيد المبلغ وتعديل سعر الصرف</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_amount">مبلغ الدفع (محسوب تلقائياً)</label>
                                                <input type="number" step="0.01" name="payment_amount" id="payment_amount" 
                                                       class="form-control bg-light" required readonly min="0.01">
                                                <small class="form-text text-info">
                                                    <i class="fas fa-info-circle"></i> المبلغ محسوب تلقائياً حسب سعر الصرف
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exchange_rate">سعر الصرف 
                                                    <button type="button" id="toggle_rate_edit" class="btn btn-sm btn-outline-secondary ml-2" disabled>
                                                        <i class="fas fa-lock" id="rate_icon"></i>
                                                    </button>
                                                </label>
                                                <input type="number" step="0.000001" name="exchange_rate" id="exchange_rate" 
                                                       class="form-control" required readonly min="0.000001">
                                                <small class="form-text text-muted" id="rate_explanation"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- ملخص التحويل -->
                                    <div id="conversion_summary" class="alert alert-info" style="display: none;">
                                        <h6><i class="fas fa-exchange-alt"></i> ملخص التحويل</h6>
                                        <div id="conversion_details"></div>
                                    </div>
                                    
                                    <!-- تحذيرات -->
                                    <div id="warnings_container"></div>
                                </div>
                            </div>

                            <!-- تاريخ الدفع -->
                            <div class="form-group">
                                <label for="payment_date">تاريخ الدفع</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control" 
                                       required value="{{ old('payment_date', date('Y-m-d')) }}">
                            </div>

                            <!-- زر الحفظ -->
                            <div class="form-group text-center">
                                <button type="submit" id="submit_payment" class="btn btn-success btn-lg" disabled>
                                    <i class="fas fa-money-bill-wave"></i> تأكيد دفع الراتب
                                </button>
                            </div>
                        </form>
                    @endif
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
$(document).ready(function() {
    // تهيئة Select2
    $('#salary_batch_id, #employee_id, #cash_account_id, #payment_currency').select2({
        width: '100%',
        dir: 'rtl',
        language: 'ar',
        placeholder: 'اختر',
        allowClear: true
    });

    // متغيرات النظام
    const salaryAmount = {{ $salary ? $salary->net_salary : 0 }};
    const salaryCurrency = '{{ $salary && $salary->employee ? $salary->employee->currency : '' }}';
    const currencies = @json(\App\Models\Currency::all());
    
    let selectedCashAccount = null;
    let selectedPaymentCurrency = null;
    let isRateEditable = false;

    // عند اختيار الصندوق النقدي
    $('#cash_account_id').change(function() {
        const accountId = $(this).val();
        
        if (accountId) {
            selectedCashAccount = {
                id: accountId,
                currency: $(this).find(':selected').data('currency'),
                balance: $(this).find(':selected').data('balance')
            };
            
            // إظهار خطوة اختيار العملة
            $('#currency_step').slideDown();
            $('#payment_currency').prop('disabled', false);
            
            // إضافة العملات المتاحة
            let currencyOptions = '<option value="">-- اختر عملة الدفع --</option>';
            currencies.forEach(function(currency) {
                currencyOptions += `<option value="${currency.code}">${currency.name} (${currency.code})</option>`;
            });
            
            $('#payment_currency').html(currencyOptions);
            
        } else {
            selectedCashAccount = null;
            $('#currency_step, #amount_step').slideUp();
            $('#payment_currency').prop('disabled', true).html('<option value="">-- اختر الصندوق أولاً --</option>');
            resetForm();
        }
    });

    // عند اختيار عملة الدفع
    $('#payment_currency').change(function() {
        const currencyCode = $(this).val();
        
        if (currencyCode) {
            selectedPaymentCurrency = currencyCode;
            
            // إظهار خطوة المبلغ وسعر الصرف
            $('#amount_step').slideDown();
            $('#toggle_rate_edit').prop('disabled', false);
            
            // حساب سعر الصرف التلقائي
            calculateExchangeRate();
            
            // حساب مبلغ الدفع تلقائياً حسب سعر الصرف
            calculatePaymentAmount();
            
            updateConversionSummary();
            
        } else {
            selectedPaymentCurrency = null;
            $('#amount_step').slideUp();
            resetForm();
        }
    });

    // عند تغيير سعر الصرف - إعادة حساب مبلغ الدفع
    $('#exchange_rate').on('input', function() {
        calculatePaymentAmount();
        updateConversionSummary();
        validateForm();
    });

    // زر تفعيل/إلغاء تعديل سعر الصرف
    $('#toggle_rate_edit').click(function() {
        isRateEditable = !isRateEditable;
        $('#exchange_rate').prop('readonly', !isRateEditable);
        
        if (isRateEditable) {
            $(this).removeClass('btn-outline-secondary').addClass('btn-warning');
            $('#rate_icon').removeClass('fa-lock').addClass('fa-unlock');
            showWarning('يمكنك الآن تعديل سعر الصرف. تأكد من دقة السعر المدخل.', 'warning');
        } else {
            $(this).removeClass('btn-warning').addClass('btn-outline-secondary');
            $('#rate_icon').removeClass('fa-unlock').addClass('fa-lock');
            calculateExchangeRate(); // إعادة حساب السعر التلقائي
            calculatePaymentAmount(); // إعادة حساب مبلغ الدفع
        }
    });

    // حساب سعر الصرف التلقائي
    function calculateExchangeRate() {
        if (!selectedPaymentCurrency || !salaryCurrency) {
            return;
        }
        
        const paymentCurrencyData = currencies.find(c => c.code === selectedPaymentCurrency);
        const salaryCurrencyData = currencies.find(c => c.code === salaryCurrency);
        
        if (!paymentCurrencyData || !salaryCurrencyData) {
            $('#exchange_rate').val(1);
            $('#rate_explanation').text('لم يتم العثور على بيانات العملة');
            return;
        }
        
        let rate = 1;
        let explanation = '';
        
        if (selectedPaymentCurrency === salaryCurrency) {
            rate = 1;
            explanation = 'نفس العملة - لا يوجد تحويل';
        } else {
            // المنطق الصحيح: كم وحدة دفع نحتاج لدفع 1 وحدة راتب؟
            // مثال: كم IQD نحتاج لدفع 1 USD راتب؟
            // الجواب: 1 USD = 1/0.000763 = 1310 IQD
            
            if (salaryCurrency === 'IQD') {
                // الراتب بالدينار، الدفع بعملة أخرى
                // 1 IQD راتب = كم USD دفع؟ الجواب: 0.000763 USD
                rate = paymentCurrencyData.exchange_rate;
                explanation = `1 ${salaryCurrency} = ${rate.toFixed(6)} ${selectedPaymentCurrency}`;
            } else if (selectedPaymentCurrency === 'IQD') {
                // الراتب بعملة أخرى، الدفع بالدينار
                // 1 USD راتب = كم IQD دفع؟ الجواب: 1/0.000763 = 1310 IQD
                rate = 1 / salaryCurrencyData.exchange_rate;
                explanation = `1 ${salaryCurrency} = ${rate.toFixed(2)} ${selectedPaymentCurrency}`;
            } else {
                // كلا العملتين ليستا دينار
                rate = paymentCurrencyData.exchange_rate / salaryCurrencyData.exchange_rate;
                explanation = `1 ${salaryCurrency} = ${rate.toFixed(6)} ${selectedPaymentCurrency}`;
            }
        }
        
        $('#exchange_rate').val(rate.toFixed(6));
        $('#rate_explanation').text(explanation);
    }

    // تحويل المبلغ بين العملات
    function convertAmount(amount, fromCurrency, toCurrency) {
        if (fromCurrency === toCurrency) return amount;
        
        const fromCurrencyData = currencies.find(c => c.code === fromCurrency);
        const toCurrencyData = currencies.find(c => c.code === toCurrency);
        
        if (!fromCurrencyData || !toCurrencyData) return amount;
        
        // التحويل عبر العملة الأساسية
        const amountInBase = amount * fromCurrencyData.exchange_rate;
        return amountInBase / toCurrencyData.exchange_rate;
    }

    // حساب مبلغ الدفع التلقائي حسب سعر الصرف
    function calculatePaymentAmount() {
        if (!selectedPaymentCurrency || !salaryCurrency) {
            return;
        }
        
        const exchangeRate = parseFloat($('#exchange_rate').val());
        
        if (isNaN(exchangeRate) || exchangeRate <= 0) {
            return;
        }
        
        let paymentAmount;
        
        if (selectedPaymentCurrency === salaryCurrency) {
            // نفس العملة - المبلغ نفسه
            paymentAmount = salaryAmount;
        } else {
            // تحويل من عملة الراتب إلى عملة الدفع
            // مثال: راتب 1200 USD × سعر صرف 1310 = 1,572,000 IQD
            paymentAmount = salaryAmount * exchangeRate;
        }
        
        $('#payment_amount').val(paymentAmount.toFixed(2));
    }

    // تحديث ملخص التحويل
    function updateConversionSummary() {
        if (!selectedPaymentCurrency || !$('#payment_amount').val()) {
            $('#conversion_summary').hide();
            return;
        }
        
        const paymentAmount = parseFloat($('#payment_amount').val());
        const exchangeRate = parseFloat($('#exchange_rate').val());
        
        if (isNaN(paymentAmount) || isNaN(exchangeRate)) return;
        
        const convertedAmount = paymentAmount * exchangeRate;
        
        let details = `
            <div class="row">
                <div class="col-md-6">
                    <strong>مبلغ الدفع:</strong> ${paymentAmount.toFixed(2)} ${selectedPaymentCurrency}
                    <br><small class="text-muted">(محسوب تلقائياً)</small>
                </div>
                <div class="col-md-6">
                    <strong>قيمة الراتب:</strong> ${convertedAmount.toFixed(2)} ${salaryCurrency}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>سعر الصرف:</strong> 1 ${selectedPaymentCurrency} = ${exchangeRate.toFixed(2)} ${salaryCurrency}
                </div>
                <div class="col-md-6">
                    <strong>صافي الراتب:</strong> ${salaryAmount.toFixed(2)} ${salaryCurrency}
                </div>
            </div>
        `;
        
        $('#conversion_details').html(details);
        $('#conversion_summary').show();
        
        // التحقق من دقة المبلغ المحول
        const difference = Math.abs(convertedAmount - salaryAmount);
        if (difference > 0.01) {
            showWarning(`تحذير: القيمة المحولة (${convertedAmount.toFixed(2)} ${salaryCurrency}) لا تطابق صافي الراتب (${salaryAmount.toFixed(2)} ${salaryCurrency}). تحقق من سعر الصرف.`, 'warning');
        } else {
            // إزالة التحذيرات إذا كان المبلغ صحيح
            $('#warnings_container').empty();
        }
        
        // التحقق من رصيد الصندوق
        if (selectedCashAccount && paymentAmount > selectedCashAccount.balance) {
            showWarning(`تحذير: المبلغ المطلوب (${paymentAmount.toFixed(2)} ${selectedPaymentCurrency}) أكبر من رصيد الصندوق (${selectedCashAccount.balance.toFixed(2)} ${selectedPaymentCurrency})`, 'danger');
        }
    }

    // عرض التحذيرات
    function showWarning(message, type = 'warning') {
        const alertClass = type === 'danger' ? 'alert-danger' : 'alert-warning';
        const icon = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
        
        $('#warnings_container').html(`
            <div class="alert ${alertClass}">
                <i class="fas ${icon}"></i> ${message}
            </div>
        `);
    }

    // التحقق من صحة النموذج
    function validateForm() {
        const isValid = selectedCashAccount && 
                       selectedPaymentCurrency && 
                       $('#payment_amount').val() && 
                       $('#exchange_rate').val() &&
                       parseFloat($('#payment_amount').val()) > 0 &&
                       parseFloat($('#exchange_rate').val()) > 0;
        
        $('#submit_payment').prop('disabled', !isValid);
    }

    // إعادة تعيين النموذج
    function resetForm() {
        $('#payment_amount, #exchange_rate').val('');
        $('#toggle_rate_edit').prop('disabled', true);
        $('#conversion_summary').hide();
        $('#warnings_container').empty();
        $('#submit_payment').prop('disabled', true);
        isRateEditable = false;
        
        // إعادة تعيين زر سعر الصرف
        $('#toggle_rate_edit').removeClass('btn-warning').addClass('btn-outline-secondary');
        $('#rate_icon').removeClass('fa-unlock').addClass('fa-lock');
        $('#exchange_rate').prop('readonly', true);
    }
});
</script>
@endpush 