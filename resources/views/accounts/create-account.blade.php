@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">@lang('messages.add_new_real_account')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-info">
                <div class="card-header">
                    <h5 class="card-title">@lang('messages.real_account_data')</h5>
                </div>
                <form action="{{ route('accounts.storeAccount') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">@lang('messages.account_name')</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('messages.account_code')</label>
                                <input type="text" id="code" class="form-control" value="{{ $nextCode }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $nextCode }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">@lang('messages.select_parent_category')</label>
                                <select id="parent_id" name="parent_id" class="form-control currency-enhanced-select" required>
                                    <option value="">-- @lang('messages.select_category') --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }} 
                                                data-currency="{{ $cat->currency ?? '' }}"
                                                style="color: {{ ($cat->currency ?? '') == 'IQD' ? '#1976d2' : (($cat->currency ?? '') == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                            {{ $cat->name }}
                                            @if($cat->currency)
                                                <span style="font-weight: bold; background: {{ ($cat->currency ?? '') == 'IQD' ? '#e3f2fd' : (($cat->currency ?? '') == 'USD' ? '#e8f5e8' : '#f3e5f5') }}; padding: 2px 6px; border-radius: 3px; font-size: 0.85em;">
                                                    {{ strtoupper($cat->currency) }}
                                                </span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">@lang('messages.account_nature')</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- @lang('messages.select_account_nature') --</option>
                                    <option value="debit" {{ old('nature')=='debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                    <option value="credit" {{ old('nature')=='credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <!-- ensure value is always sent: 0 if unchecked -->
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" name="is_cash_box" value="1" class="form-check-input" id="cashBoxCheck"
                                {{ old('is_cash_box') ? 'checked' : '' }}>
                            <label class="form-check-label" for="cashBoxCheck">@lang('messages.is_cash_box')</label>
                        </div>

                        <!-- نظام العملات المتعددة -->
                        <div class="alert alert-info border-left-primary shadow h-100 py-2">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <i class="fas fa-coins"></i> نظام العملات المتعددة
                                    </div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        سيدعم هذا الحساب جميع العملات النشطة تلقائياً
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle text-success"></i> IQD (الدينار العراقي) - عملة افتراضية
                                        <br>
                                        <i class="fas fa-check-circle text-success"></i> USD (الدولار الأمريكي)
                                        <br>
                                        <i class="fas fa-check-circle text-success"></i> EUR (اليورو)
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-globe fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>

                        <!-- قسم الرصيد الافتتاحي -->
                        <div class="form-group form-check">
                            <input type="checkbox" name="has_opening_balance" value="1" class="form-check-input" id="openingBalanceCheck"
                                {{ old('has_opening_balance') ? 'checked' : '' }} onchange="toggleOpeningBalance()">
                            <label class="form-check-label" for="openingBalanceCheck">
                                <strong>إضافة رصيد افتتاحي</strong>
                            </label>
                        </div>

                        <!-- تفاصيل الرصيد الافتتاحي -->
                        <div id="openingBalanceSection" style="display: none;" class="border p-3 bg-light rounded">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="opening_balance_amount">مبلغ الرصيد الافتتاحي</label>
                                    <input type="number" step="0.01" id="opening_balance_amount" name="opening_balance_amount" 
                                           value="{{ old('opening_balance_amount') }}" class="form-control" 
                                           placeholder="0.00">
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label for="opening_balance_currency">عملة الرصيد الافتتاحي</label>
                                    <select id="opening_balance_currency" name="opening_balance_currency" class="form-control">
                                        <option value="">-- اختر العملة --</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->code }}" 
                                                    {{ old('opening_balance_currency', 'IQD') == $currency->code ? 'selected' : '' }}
                                                    style="color: {{ $currency->code == 'IQD' ? '#1976d2' : ($currency->code == 'USD' ? '#388e3c' : '#5e35b1') }};">
                                                {{ $currency->code }} - {{ $currency->name }}
                                                @if($currency->is_default)
                                                    <span style="font-weight: bold;">(افتراضي)</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label for="opening_balance_type">نوع الرصيد</label>
                                    <select id="opening_balance_type" name="opening_balance_type" class="form-control">
                                        <option value="">-- اختر نوع الرصيد --</option>
                                        <option value="debit" {{ old('opening_balance_type') == 'debit' ? 'selected' : '' }}>مدين</option>
                                        <option value="credit" {{ old('opening_balance_type') == 'credit' ? 'selected' : '' }}>دائن</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="opening_balance_date">تاريخ الرصيد الافتتاحي</label>
                                <input type="date" id="opening_balance_date" name="opening_balance_date" 
                                       value="{{ old('opening_balance_date', date('Y-m-d')) }}" class="form-control">
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>ملاحظة:</strong> الرصيد الافتتاحي يمكن إدخاله مرة واحدة فقط عند إنشاء الحساب. 
                                يمكن تعديله لاحقاً فقط في حالة عدم وجود أي حركات مالية في الحساب.
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">@lang('messages.save')</button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function refreshAccountCode(){
        let isGroup = 0;
        let parent_id = $('#parent_id').val() || '';
        
        // تنفيذ طلب AJAX فقط إذا تم اختيار فئة أب
        if (parent_id) {
            // إضافة معلمة عشوائية لمنع التخزين المؤقت
            $.getJSON("{{ route('accounts.nextCode') }}?_=" + new Date().getTime(), 
                { is_group: isGroup, parent_id: parent_id }, 
                function(data){
                    if (data.nextCode) {
                        $('#code').val(data.nextCode);
                        $('#codeInput').val(data.nextCode);
                        console.log("تم تحديث الكود إلى: " + data.nextCode); // سجل للتصحيح
                    } else {
                        console.warn("تم استلام استجابة من الخادم لكن بدون كود"); // سجل للتصحيح
                    }
                }
            ).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("خطأ في الحصول على الكود: " + textStatus, errorThrown);
            });
        } else {
            // إذا لم يتم اختيار فئة أب، عرض رسالة إرشادية بالعربية
            $('#code').val("اختر فئة أولاً");
            $('#codeInput').val("");
        }
    }
    
    // تنفيذ عند تغيير الاختيار
    $('#parent_id').on('change', refreshAccountCode);
    
    // التأكد من تحديث الكود عند تحميل الصفحة إذا كانت هناك قيمة محددة مسبقاً
    setTimeout(function() {
        if ($('#parent_id').val()) {
            refreshAccountCode();
        }
    }, 500); // تأخير قصير للتأكد من تحميل الصفحة بالكامل
});

// دالة إظهار/إخفاء قسم الرصيد الافتتاحي
function toggleOpeningBalance() {
    const checkbox = document.getElementById('openingBalanceCheck');
    const section = document.getElementById('openingBalanceSection');
    
    if (checkbox.checked) {
        section.style.display = 'block';
        // جعل الحقول مطلوبة عند التفعيل
        document.getElementById('opening_balance_amount').required = true;
        document.getElementById('opening_balance_currency').required = true;
        document.getElementById('opening_balance_type').required = true;
        document.getElementById('opening_balance_date').required = true;
    } else {
        section.style.display = 'none';
        // إزالة خاصية المطلوب عند الإلغاء
        document.getElementById('opening_balance_amount').required = false;
        document.getElementById('opening_balance_currency').required = false;
        document.getElementById('opening_balance_type').required = false;
        document.getElementById('opening_balance_date').required = false;
        // مسح القيم
        document.getElementById('opening_balance_amount').value = '';
        document.getElementById('opening_balance_currency').value = '';
        document.getElementById('opening_balance_type').value = '';
    }
}

// إظهار القسم إذا كان مفعل مسبقاً (في حالة الأخطاء)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('openingBalanceCheck').checked) {
        toggleOpeningBalance();
    }
});
</script>
@endpush
