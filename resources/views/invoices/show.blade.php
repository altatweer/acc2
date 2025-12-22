@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">@lang('messages.invoice_details', ['number' => $invoice->invoice_number])</h1>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <h5>@lang('messages.invoice_info')</h5>
        <table class="table table-bordered">
          <tr><th>@lang('messages.invoice_id')</th><td>{{ $invoice->invoice_number }}</td></tr>
          <tr><th>@lang('messages.customer')</th><td>{{ $invoice->customer->name }}</td></tr>
          <tr><th>@lang('messages.date')</th><td>{{ $invoice->date->format('Y-m-d') }}</td></tr>
          <tr><th>@lang('messages.total')</th><td>{{ number_format($invoice->total,2) }} {{ $invoice->currency }}</td></tr>
          <tr><th>@lang('messages.status')</th><td>
            @php
              $statusLabels = [
                'draft'=>__('messages.status_draft'),
                'unpaid'=>__('messages.status_unpaid'),
                'partial'=>__('messages.status_partial'),
                'paid'=>__('messages.status_paid')
              ];
            @endphp
            <span class="badge badge-{{ $invoice->status=='draft'?'secondary':($invoice->status=='unpaid'?'warning':($invoice->status=='partial'?'info':'success')) }}">
              {{ $statusLabels[$invoice->status] ?? $invoice->status }}
            </span>
          </td></tr>
          <tr><th>@lang('messages.paid_amount')</th><td>{{ number_format($invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}</td></tr>
          <tr><th>@lang('messages.remaining_amount')</th><td>{{ number_format($invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount'),2) }} {{ $invoice->currency }}</td></tr>
        </table>

        <hr>
        <h5>@lang('messages.invoice_line_items')</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>@lang('messages.item_hash')</th>
                <th>@lang('messages.item')</th>
                <th>@lang('messages.quantity')</th>
                <th>@lang('messages.unit_price_short')</th>
                <th>@lang('messages.line_total')</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoice->invoiceItems as $i => $item)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->item->name }} ({{ $item->item->type }})</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price,2) }}</td>
                <td>{{ number_format($item->line_total,2) }}</td>
              </tr>
              @endforeach
              @if($invoice->invoiceItems->isEmpty())
              <tr><td colspan="5" class="text-center">@lang('messages.no_items_in_invoice')</td></tr>
              @endif
            </tbody>
          </table>
        </div>

        <hr>
        <h5>@lang('messages.previous_payments')</h5>
        <table class="table table-bordered table-striped">
          <thead><tr><th>@lang('messages.item_hash')</th><th>@lang('messages.voucher_id')</th><th>@lang('messages.date')</th><th>@lang('messages.amount')</th><th>@lang('messages.actions')</th></tr></thead>
          <tbody>
            @foreach($payments as $i=>$vch)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $vch->voucher_number }}</td>
              <td>{{ $vch->date }}</td>
              <td>
                @php
                  $convertedAmount = $vch->transactions->sum('amount'); // المبلغ بعملة الفاتورة
                  $originalAmount = null;
                  $originalCurrency = $vch->currency;
                  
                  // البحث عن المبلغ الأصلي من قيود المحاسبة
                  if ($vch->journalEntry) {
                    $debitLine = $vch->journalEntry->lines->where('debit', '>', 0)->first();
                    if ($debitLine && $debitLine->currency !== $invoice->currency) {
                      $originalAmount = $debitLine->debit;
                    }
                  }
                @endphp
                
                {{ number_format($convertedAmount, 2) }} {{ $invoice->currency }}
                @if($originalAmount && $originalCurrency !== $invoice->currency)
                  <small class="text-muted">({{ number_format($originalAmount, 0) }} {{ $originalCurrency }})</small>
                @endif
              </td>
              <td><a href="{{ Route::localizedRoute('vouchers.show', ['voucher' => $vch->id]) }}" class="btn btn-sm btn-info">@lang('messages.view_voucher')</a></td>
            </tr>
            @endforeach
            @if(count($payments)==0)
              <tr><td colspan="5" class="text-center">@lang('messages.no_payments_yet')</td></tr>
            @endif
          </tbody>
        </table>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($invoice->status=='paid')
        <div class="alert alert-success">@lang('messages.invoice_paid_fully_no_new_payments')</div>
        @endif

        @if(in_array($invoice->status, ['unpaid','partial']))
        <hr>
        <h5>@lang('messages.new_payment')</h5>
        <form action="{{ Route::localizedRoute('invoice-payments.store') }}" method="POST" class="mt-3" id="paymentForm">
          @csrf
          <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
          
          <div class="row">
            <!-- اختيار الصندوق النقدي -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="cash_account_id">
                  <i class="fas fa-cash-register text-success"></i>
                  اختيار الصندوق النقدي
                </label>
                <select name="cash_account_id" id="cash_account_id" class="form-control select2" required>
                  <option value="">-- اختر الصندوق النقدي --</option>
                  @foreach($cashAccounts as $acc)
                    <option value="{{ $acc->id }}">
                      {{ $acc->code }} - {{ $acc->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <!-- اختيار عملة السداد -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_currency">
                  <i class="fas fa-coins text-warning"></i>
                  عملة السداد
                </label>
                <select name="payment_currency" id="payment_currency" class="form-control" required disabled>
                  <option value="">-- اختر الصندوق أولاً --</option>
                </select>
                <small class="text-muted">اختر العملة التي تريد السداد بها من الصندوق المختار</small>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- تاريخ السداد -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_date">
                  <i class="fas fa-calendar text-primary"></i>
                  @lang('messages.payment_date')
                </label>
                <input type="date" name="date" id="payment_date" class="form-control" 
                       value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            
            <!-- مبلغ السداد -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_amount">
                  <i class="fas fa-money-bill-wave text-success"></i>
                  <span id="payment_amount_label">مبلغ السداد</span>
                </label>
                <input type="number" name="payment_amount" id="payment_amount" 
                       class="form-control" step="0.01" required 
                       placeholder="0.00" disabled>
                <small class="text-muted">
                  <span id="required_amount_info">المبلغ المطلوب: {{ number_format($invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount'), 2) }} {{ $invoice->currency }}</span>
                </small>
              </div>
            </div>
          </div>

          <!-- حقول التحويل (تظهر فقط عند اختلاف العملات) -->
          <div class="row" id="conversion_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="exchange_rate">
                  <i class="fas fa-exchange-alt text-warning"></i>
                  سعر الصرف
                  <button type="button" id="edit_rate_btn" class="btn btn-sm btn-outline-secondary ml-1">
                    <i class="fas fa-edit"></i> تعديل
                  </button>
                </label>
                <input type="number" name="exchange_rate" id="exchange_rate" 
                       class="form-control" step="0.0000000001" readonly>
                <small class="text-muted" id="rate_info"></small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="converted_amount">
                  <i class="fas fa-calculator text-info"></i>
                  <span id="converted_amount_label">المبلغ المحول إلى {{ $invoice->currency }}</span>
                </label>
                <input type="number" id="converted_amount" class="form-control" readonly>
                <small class="text-muted" id="conversion_info"></small>
              </div>
            </div>
          </div>

          <!-- تحذيرات -->
          <div id="currency_conversion_alert" class="alert alert-info" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <span id="conversion_alert_text"></span>
          </div>

          <div id="same_currency_alert" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            السداد بنفس عملة الفاتورة - لا حاجة للتحويل
          </div>

          <div id="insufficient_funds_warning" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="insufficient_funds_text"></span>
          </div>

          <div id="amount_warning" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            مبلغ السداد يتجاوز المبلغ المطلوب
          </div>

          <button type="submit" class="btn btn-success btn-lg" id="pay_button" disabled>
            <i class="fas fa-credit-card"></i>
            @lang('messages.pay_button')
          </button>
          <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="resetForm()">
            <i class="fas fa-undo"></i>
            إعادة تعيين
          </button>
        </form>
        @endif

        <div class="mt-4 mb-2">
          @if($invoice->status=='draft')
            <form action="{{ Route::localizedRoute('invoices.approve', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              <button type="submit" class="btn btn-success">@lang('messages.approve_invoice')</button>
            </form>
            <a href="{{ Route::localizedRoute('invoices.edit', ['invoice' => $invoice, ]) }}" class="btn btn-primary">@lang('messages.edit')</a>
            <form action="{{ Route::localizedRoute('invoices.destroy', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.delete_invoice_confirm')')">@lang('messages.delete')</button>
            </form>
          @elseif($invoice->status=='unpaid')
            <form action="{{ Route::localizedRoute('invoices.cancel', ['invoice' => $invoice, ]) }}" method="POST" style="display:inline-block;">
              @csrf
              <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.cancel_invoice_confirm')')">@lang('messages.cancel_invoice')</button>
            </form>
          @elseif($invoice->status=='partial')
            <div class="alert alert-info">@lang('messages.cannot_cancel_partial_payments')</div>
          @elseif($invoice->status=='paid')
            <div class="alert alert-success">@lang('messages.invoice_paid_cannot_edit_cancel')</div>
          @endif
        </div>

        <div class="mb-3 text-center">
            <a href="{{ Route::localizedRoute('invoices.print', ['invoice' => $invoice, ]) }}" class="btn btn-primary" target="_blank"><i class="fa fa-print"></i> @lang('messages.print_invoice')</a>
        </div>

      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
// دالة لتنسيق الأرقام
function formatNumber(num) {
    if (typeof num === 'string') {
        num = parseFloat(num);
    }
    if (isNaN(num)) return '0';
    return num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

$(document).ready(function(){
    const invoiceCurrency = '{{ $invoice->currency }}';
    const invoiceAmount = {{ $invoice->total - $invoice->transactions()->where('type','receipt')->sum('amount') }};
    const currencies = @json($currencies);
    const cashAccounts = @json($cashAccounts);
    
    // التحقق من تحميل البيانات
    console.log('Script loaded successfully!');
    console.log('Invoice currency:', invoiceCurrency);
    console.log('Invoice amount:', invoiceAmount);
    console.log('Currencies:', currencies);
    console.log('Cash accounts:', cashAccounts);
    
    let selectedCashAccount = null;
    let selectedPaymentCurrency = null;
    let isRateEditable = false;

    // حل مباشر ومبسط - عند اختيار الصندوق النقدي
    $('#cash_account_id').change(function(){
        var accountId = $(this).val();
        
        if (accountId) {
            // تفعيل حقل العملة فوراً
            $('#payment_currency').prop('disabled', false);
            
            // تعيين الصندوق المختار
            selectedCashAccount = {id: accountId};
            
            // إضافة العملات
            var currencyOptions = '<option value="">-- اختر عملة السداد --</option>';
            @foreach($currencies as $currency)
                currencyOptions += '<option value="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->code }})</option>';
            @endforeach
            
            $('#payment_currency').html(currencyOptions);
        } else {
            selectedCashAccount = null;
            $('#payment_currency').prop('disabled', true).html('<option value="">-- اختر الصندوق أولاً --</option>');
            $('#payment_amount').prop('disabled', true).val('');
        }
    });
    
    // عند اختيار العملة - تفعيل حقل المبلغ
    $('#payment_currency').change(function(){
        var currency = $(this).val();
        
        if (currency) {
            $('#payment_amount').prop('disabled', false);
            selectedPaymentCurrency = currency;
            updatePaymentForm();
        } else {
            selectedPaymentCurrency = null;
            $('#payment_amount').prop('disabled', true).val('');
        }
    });

    // تفعيل/إلغاء تفعيل تعديل سعر الصرف
    $('#edit_rate_btn').on('click', function(){
        isRateEditable = !isRateEditable;
        $('#exchange_rate').prop('readonly', !isRateEditable);
        
        if (isRateEditable) {
            $(this).html('<i class="fas fa-lock"></i> قفل');
            $(this).removeClass('btn-outline-secondary').addClass('btn-warning');
            $('#exchange_rate').focus();
        } else {
            $(this).html('<i class="fas fa-edit"></i> تعديل');
            $(this).removeClass('btn-warning').addClass('btn-outline-secondary');
        }
    });

    // حساب المبلغ المحول عند تغيير المبلغ أو سعر الصرف
    $('#payment_amount, #exchange_rate').on('input', function(){
        calculateConvertedAmount();
        validatePayment();
    });

    function loadCurrenciesForAccount() {
        console.log('Loading currencies for account...');
        console.log('Currencies data:', currencies);
        
        // تحميل العملات المتاحة (جميع العملات) - مبسط
        let options = '<option value="">-- اختر عملة السداد --</option>';
        
        if (currencies && currencies.length > 0) {
            currencies.forEach(currency => {
                options += `<option value="${currency.code}">${currency.name} (${currency.code})</option>`;
            });
        } else {
            console.error('No currencies available');
            options += '<option value="">لا توجد عملات متاحة</option>';
        }

        $('#payment_currency').html(options).prop('disabled', false);
        console.log('Payment currency field enabled with options:', options);
    }

    function updatePaymentForm() {
        console.log('updatePaymentForm called');
        console.log('selectedPaymentCurrency:', selectedPaymentCurrency);
        
        if (!selectedPaymentCurrency) {
            console.log('No payment currency selected');
            return;
        }

        const sameCurrency = selectedPaymentCurrency === invoiceCurrency;
        console.log('Same currency check:', sameCurrency, 'Payment:', selectedPaymentCurrency, 'Invoice:', invoiceCurrency);
        
        // تحديث التسميات وتفعيل حقل المبلغ
        $('#payment_amount_label').text(`مبلغ السداد (${selectedPaymentCurrency})`);
        $('#payment_amount').prop('disabled', false).focus();
        console.log('Payment amount field enabled');
        
        // إخفاء التحذيرات أولاً
        $('#amount_warning, #insufficient_funds_warning').hide();
        
        if (sameCurrency) {
            // نفس العملة - إخفاء حقول التحويل
            $('#conversion_section').hide();
            $('#currency_conversion_alert').hide();
            $('#same_currency_alert').show();
            $('#exchange_rate').val(1);
            $('#converted_amount').val('');
        } else {
            // عملات مختلفة - إظهار حقول التحويل
            $('#conversion_section').show();
            $('#same_currency_alert').hide();
            
            // حساب سعر الصرف الصحيح - عرض مثل السندات (مثل 1420 أو 1310)
            const selectedCurrencyData = currencies.find(c => c.code === selectedPaymentCurrency);
            const invoiceCurrencyData = currencies.find(c => c.code === invoiceCurrency);
            
            console.log('Currency data:', {
                selectedPaymentCurrency,
                invoiceCurrency,
                selectedCurrencyData,
                invoiceCurrencyData,
                allCurrencies: currencies
            });
            
            // التحقق من وجود بيانات العملات
            if (!selectedCurrencyData || !invoiceCurrencyData) {
                console.error('Currency data not found:', {
                    selectedPaymentCurrency,
                    invoiceCurrency,
                    selectedCurrencyData,
                    invoiceCurrencyData,
                    availableCurrencies: currencies.map(c => c.code)
                });
                $('#exchange_rate').val(1);
                $('#rate_info').text('بيانات العملة غير متوفرة');
                return;
            }
            
            let defaultRate;
            let isInverse = false; // لتحديد إذا كان يجب القسمة أو الضرب
            
            // منطق مبسط لحساب السعر - عرض مثل السندات
            // ملاحظة: exchange_rate في جدول currencies هو سعر العملة مقابل IQD
            // IQD: exchange_rate = 1.0
            // USD: exchange_rate قد يكون 0.000763 (يعني 1 IQD = 0.000763 USD) أو 1310 (يعني 1 USD = 1310 IQD)
            
            console.log('Exchange rates:', {
                selected: selectedCurrencyData.exchange_rate,
                invoice: invoiceCurrencyData.exchange_rate
            });
            
            if (selectedPaymentCurrency === 'IQD' && invoiceCurrency === 'USD') {
                // السداد بالدينار والفاتورة بالدولار
                // عرض: 1 USD = 1310.6 IQD (مثل السندات)
                const usdRate = parseFloat(invoiceCurrencyData.exchange_rate) || 0;
                
                if (usdRate > 1) {
                    // قيمة مباشرة: exchange_rate = 1310 (يعني 1 USD = 1310 IQD)
                    defaultRate = usdRate;
                    console.log('Using direct rate:', defaultRate);
                } else if (usdRate > 0 && usdRate < 1) {
                    // قيمة عكسية: exchange_rate = 0.000763 (يعني 1 IQD = 0.000763 USD)
                    defaultRate = 1 / usdRate; // 1 ÷ 0.000763 = 1310.6
                    console.log('Using inverse rate, calculated:', defaultRate);
                } else {
                    // قيمة افتراضية
                    defaultRate = 1310; // قيمة افتراضية
                    console.log('Using default rate:', defaultRate);
                }
                isInverse = true; // يجب القسمة عند التحويل
                $('#rate_info').text(`1 ${invoiceCurrency} = ${defaultRate.toFixed(2)} ${selectedPaymentCurrency}`);
            } else if (selectedPaymentCurrency === 'USD' && invoiceCurrency === 'IQD') {
                // السداد بالدولار والفاتورة بالدينار
                // عرض: 1 USD = 1310.6 IQD (مثل السندات)
                const usdRate = parseFloat(selectedCurrencyData.exchange_rate) || 0;
                
                if (usdRate > 1) {
                    // قيمة مباشرة: exchange_rate = 1310 (يعني 1 USD = 1310 IQD)
                    defaultRate = usdRate;
                    console.log('Using direct rate:', defaultRate);
                } else if (usdRate > 0 && usdRate < 1) {
                    // قيمة عكسية: exchange_rate = 0.000763 (يعني 1 IQD = 0.000763 USD)
                    defaultRate = 1 / usdRate; // 1 ÷ 0.000763 = 1310.6
                    console.log('Using inverse rate, calculated:', defaultRate);
                } else {
                    // قيمة افتراضية
                    defaultRate = 1310; // قيمة افتراضية
                    console.log('Using default rate:', defaultRate);
                }
                isInverse = false; // يجب الضرب عند التحويل
                $('#rate_info').text(`1 ${selectedPaymentCurrency} = ${defaultRate.toFixed(2)} ${invoiceCurrency}`);
            } else {
                // للعملات الأخرى - حساب نسبي
                let selectedRate = parseFloat(selectedCurrencyData.exchange_rate) || 1;
                let invoiceRate = parseFloat(invoiceCurrencyData.exchange_rate) || 1;
                
                // تحويل إلى قيم مباشرة إذا كانت عكسية
                if (selectedRate < 1 && selectedRate > 0) {
                    selectedRate = 1 / selectedRate;
                }
                if (invoiceRate < 1 && invoiceRate > 0) {
                    invoiceRate = 1 / invoiceRate;
                }
                
                // حساب السعر من عملة السداد إلى عملة الفاتورة
                if (selectedRate < invoiceRate) {
                    // عملة السداد أصغر - مثل IQD إلى USD
                    defaultRate = invoiceRate / selectedRate;
                    isInverse = true;
                    $('#rate_info').text(`1 ${invoiceCurrency} = ${defaultRate.toFixed(2)} ${selectedPaymentCurrency}`);
                } else {
                    // عملة السداد أكبر - مثل USD إلى IQD
                    defaultRate = selectedRate / invoiceRate;
                    isInverse = false;
                    $('#rate_info').text(`1 ${selectedPaymentCurrency} = ${defaultRate.toFixed(2)} ${invoiceCurrency}`);
                }
            }
            
            // التحقق من أن السعر صحيح
            if (isNaN(defaultRate) || defaultRate <= 0 || !isFinite(defaultRate)) {
                console.error('Invalid exchange rate calculated:', defaultRate);
                defaultRate = 1310; // قيمة افتراضية آمنة
                isInverse = selectedPaymentCurrency === 'IQD' && invoiceCurrency === 'USD';
            }
            
            console.log('Final exchange rate:', defaultRate, 'isInverse:', isInverse);
            
            // حفظ معلومات السعر للاستخدام في حساب المبلغ المحول
            $('#exchange_rate').data('is-inverse', isInverse);
            $('#exchange_rate').val(defaultRate.toFixed(4)); // عرض 4 أرقام عشرية مثل السندات
            
            // إظهار تحذير التحويل
            $('#currency_conversion_alert').show();
            $('#conversion_alert_text').text(
                `سيتم السداد بـ ${selectedPaymentCurrency} وتحويل المبلغ إلى ${invoiceCurrency} حسب سعر الصرف`
            );
        }
        
        calculateConvertedAmount();
        validatePayment();
    }

    function calculateConvertedAmount() {
        console.log('calculateConvertedAmount called');
        if (!selectedPaymentCurrency) {
            console.log('No payment currency selected, returning');
            return;
        }
        
        const amount = parseFloat($('#payment_amount').val()) || 0;
        const rate = parseFloat($('#exchange_rate').val()) || 1;
        
        console.log('Calculate conversion:', {
            amount: amount,
            rate: rate,
            paymentCurrency: selectedPaymentCurrency,
            invoiceCurrency: invoiceCurrency
        });
        
        if (selectedPaymentCurrency !== invoiceCurrency) {
            let convertedAmount;
            const isInverse = $('#exchange_rate').data('is-inverse') || false;
            
            if (isInverse) {
                // للعملات المقلوبة مثل IQD → USD: القسمة
                // مثال: 24,345,985.2 IQD ÷ 1310.6 = 18,575.99 USD
                convertedAmount = amount / rate;
                $('#conversion_info').text(
                    `${formatNumber(amount)} ${selectedPaymentCurrency} ÷ ${rate.toFixed(2)} = ${formatNumber(convertedAmount)} ${invoiceCurrency}`
                );
            } else {
                // للعملات العادية مثل USD → IQD: الضرب
                // مثال: 100 USD × 1310.6 = 131,060 IQD
                convertedAmount = amount * rate;
                $('#conversion_info').text(
                    `${formatNumber(amount)} ${selectedPaymentCurrency} × ${rate.toFixed(2)} = ${formatNumber(convertedAmount)} ${invoiceCurrency}`
                );
            }
            
            $('#converted_amount').val(convertedAmount.toFixed(2));
        } else {
            $('#converted_amount').val(amount.toFixed(2));
        }
    }

    function validatePayment() {
        console.log('validatePayment called');
        
        const paymentAmount = parseFloat($('#payment_amount').val()) || 0;
        console.log('Payment amount:', paymentAmount);
        console.log('Selected payment currency:', selectedPaymentCurrency);
        
        // التحقق من اختيار العملة
        if (!selectedPaymentCurrency) {
            console.log('No payment currency selected - disabling pay button');
            $('#pay_button').prop('disabled', true);
            return;
        }
        
        // التحقق من وجود مبلغ
        if (paymentAmount <= 0) {
            console.log('Payment amount is zero or negative - disabling pay button');
            $('#pay_button').prop('disabled', true);
            return;
        }

        const convertedAmount = selectedPaymentCurrency === invoiceCurrency ? 
                               paymentAmount : 
                               parseFloat($('#converted_amount').val()) || 0;
        
        console.log('Converted amount:', convertedAmount);
        console.log('Invoice amount needed:', invoiceAmount);
        
        // فحص تجاوز المبلغ المطلوب
        const exceedsRequired = convertedAmount > invoiceAmount;
        $('#amount_warning').toggle(exceedsRequired);
        
        console.log('Exceeds required:', exceedsRequired);
        
        // تفعيل/إلغاء تفعيل زر السداد
        const canPay = paymentAmount > 0 && !exceedsRequired;
        console.log('Can pay:', canPay);
        $('#pay_button').prop('disabled', !canPay);
        
        if (canPay) {
            console.log('✅ Pay button ENABLED');
        } else {
            console.log('❌ Pay button DISABLED');
        }
    }

    function resetPaymentCurrency() {
        selectedPaymentCurrency = null;
        $('#payment_currency').val('').prop('disabled', true);
        resetPaymentForm();
    }

    function resetPaymentForm() {
        $('#payment_amount, #exchange_rate, #converted_amount').val('');
        $('#payment_amount').prop('disabled', true);
        $('#conversion_section, #currency_conversion_alert, #same_currency_alert').hide();
        $('#insufficient_funds_warning, #amount_warning').hide();
        $('#pay_button').prop('disabled', true);
        isRateEditable = false;
        $('#edit_rate_btn').html('<i class="fas fa-edit"></i> تعديل')
            .removeClass('btn-warning').addClass('btn-outline-secondary');
        $('#exchange_rate').prop('readonly', true);
    }

    // إعادة تعيين النموذج
    window.resetForm = function() {
        $('#cash_account_id').val('').trigger('change');
        resetPaymentCurrency();
        resetPaymentForm();
    };
    
    // إضافة debugging للنموذج
    $('#paymentForm').on('submit', function(e) {
        console.log('Form is being submitted');
        console.log('Form data:', {
            cash_account_id: $('#cash_account_id').val(),
            payment_currency: $('#payment_currency').val(),
            payment_amount: $('#payment_amount').val(),
            exchange_rate: $('#exchange_rate').val(),
            date: $('#payment_date').val()
        });
        
        // التحقق من البيانات قبل الإرسال
        if (!$('#cash_account_id').val()) {
            e.preventDefault();
            alert('يرجى اختيار الصندوق النقدي');
            return false;
        }
        
        if (!$('#payment_currency').val()) {
            e.preventDefault();
            alert('يرجى اختيار عملة السداد');
            return false;
        }
        
        if (!$('#payment_amount').val() || parseFloat($('#payment_amount').val()) <= 0) {
            e.preventDefault();
            alert('يرجى إدخال مبلغ السداد');
            return false;
        }
        
        console.log('Form validation passed, submitting...');
    });
});
</script>
@endpush
@endsection 