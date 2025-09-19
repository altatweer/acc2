@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4 text-center">
                <i class="fas fa-plus-circle"></i> إنشاء قيد أحادي العملة
            </h2>
        </div>
    </div>
    
    <form action="{{ route('journal-entries.store-single-currency') }}" method="POST" id="journalForm">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3>تفاصيل القيد</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>العملة</label>
                        <select name="currency" class="form-control" required>
                            @foreach($currencies as $curr)
                                <option value="{{ $curr->code }}" {{ $curr->code === $defaultCurrency ? 'selected' : '' }}>{{ $curr->code }} - {{ $curr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>الوصف</label>
                        <input type="text" name="description" class="form-control" placeholder="وصف القيد" required>
                    </div>
                </div>
                
                <table class="table table-bordered" id="linesTable">
                    <thead>
                        <tr>
                            <th style="width: 35%">الحساب</th>
                            <th style="width: 30%">الوصف</th>
                            <th style="width: 15%">مدين</th>
                            <th style="width: 15%">دائن</th>
                            <th style="width: 5%">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control account-search" placeholder="ابحث عن الحساب..." autocomplete="off">
                                <input type="hidden" name="lines[0][account_id]" class="account-id-field" required>
                                <div class="account-suggestions" style="display: none;"></div>
                            </td>
                            <td><input type="text" name="lines[0][description]" class="form-control" placeholder="وصف العملية"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control debit" step="0.01" value="0" min="0" placeholder="0.00"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control credit" step="0.01" value="0" min="0" placeholder="0.00"></td>
                            <td>
                                <input type="hidden" name="lines[0][currency]" value="{{ old('currency', $defaultCurrency) }}" class="line-currency">
                                <input type="hidden" name="lines[0][exchange_rate]" value="1" class="line-exchange-rate">
                                <button type="button" class="btn btn-danger btn-sm remove-line" title="حذف السطر">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="form-control account-search" placeholder="ابحث عن الحساب..." autocomplete="off">
                                <input type="hidden" name="lines[1][account_id]" class="account-id-field" required>
                                <div class="account-suggestions" style="display: none;"></div>
                            </td>
                            <td><input type="text" name="lines[1][description]" class="form-control" placeholder="وصف العملية"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit" step="0.01" value="0" min="0" placeholder="0.00"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit" step="0.01" value="0" min="0" placeholder="0.00"></td>
                            <td>
                                <input type="hidden" name="lines[1][currency]" value="{{ old('currency', $defaultCurrency) }}" class="line-currency">
                                <input type="hidden" name="lines[1][exchange_rate]" value="1" class="line-exchange-rate">
                                <button type="button" class="btn btn-danger btn-sm remove-line" title="حذف السطر">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-primary" id="addLine">
                    <i class="fas fa-plus"></i> إضافة سطر جديد
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> حفظ القيد
                </button>
                <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة للقيود
                </a>
            </div>
        </div>
    </form>
    
    <!-- بيانات الحسابات المخفية للجافا سكريبت -->
    <script type="text/javascript">
        window.accountsData = @json($accounts->map(function($acc) { return ['id' => $acc->id, 'text' => ($acc->code ? $acc->code . ' - ' . $acc->name : $acc->name)]; }));
    </script>
</div>
@endsection

@push('styles')
<style>
/* بحث بسيط - بدون مكتبات خارجية */
.account-search {
    font-size: 14px;
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: white;
    text-align: right;
    direction: rtl;
}

.account-search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

.account-search.selected {
    background-color: #e8f4fd;
    border-color: #007bff;
    color: #495057;
    font-weight: 500;
}

.account-search.invalid {
    border-color: #dc3545;
    background-color: #ffe6e6;
}

/* اقتراحات البحث */
.account-suggestions {
    position: absolute;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    text-align: right;
    direction: rtl;
}

.suggestion-item:hover {
    background-color: #f0f8ff;
}

.suggestion-item:last-child {
    border-bottom: none;
}

#linesTable th {
    background-color: #f8f9fa;
    text-align: center;
    font-weight: 600;
    border: 1px solid #dee2e6;
}

#linesTable td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
    position: relative;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #ced4da;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
    border-radius: 4px;
}

.table {
    margin-bottom: 0;
}

.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border: 1px solid #e3e6ea;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6ea;
}

.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e3e6ea;
}
</style>
@endpush

@push('scripts')
<script>
let lineIdx = 2;

$(document).ready(function(){
    console.log('🚀 تم تحميل الصفحة، البيانات المتوفرة:', window.accountsData?.length || 0, 'حساب');
    console.log('🗂️ بيانات الحسابات:', window.accountsData);
    
    // بحث الحسابات - حل بسيط وقوي
    $(document).on('input focus', '.account-search', function() {
        let $input = $(this);
        let $row = $input.closest('tr');
        let $hiddenInput = $row.find('.account-id-field');
        let $suggestions = $row.find('.account-suggestions');
        let searchValue = $input.val().trim();
        
        console.log('🔍 البحث عن:', searchValue);
        
        if (searchValue.length < 1) {
            $suggestions.hide().empty();
            $hiddenInput.val('');
            $input.removeClass('selected invalid');
            return;
        }
        
        // فلترة الحسابات
        let matches = window.accountsData.filter(account => 
            account.text.toLowerCase().includes(searchValue.toLowerCase())
        );
        
        console.log('📋 تم العثور على', matches.length, 'نتيجة');
        
        if (matches.length > 0) {
            let html = '';
            matches.slice(0, 10).forEach(account => {
                html += `<div class="suggestion-item" data-id="${account.id}" data-text="${account.text}">${account.text}</div>`;
            });
            
            $suggestions.html(html).show();
        } else {
            $suggestions.hide().empty();
            $input.addClass('invalid').removeClass('selected');
            $hiddenInput.val('');
        }
    });
    
    // اختيار الحساب
    $(document).on('click', '.suggestion-item', function() {
        let $item = $(this);
        let accountId = $item.data('id');
        let accountText = $item.data('text');
        
        // الحصول على الـ row الصحيح
        let $row = $item.closest('tr');
        let $suggestions = $item.parent();
        let $input = $row.find('.account-search');
        let $hiddenInput = $row.find('.account-id-field');
        
        $input.val(accountText).addClass('selected').removeClass('invalid');
        $hiddenInput.val(accountId);
        $suggestions.hide().empty();
        
        console.log('✅ تم اختيار الحساب:', accountId, '-', accountText);
        console.log('💾 القيمة المحفوظة:', $hiddenInput.attr('name'), '=', $hiddenInput.val());
        console.log('🎯 في الصف:', $row.index() + 1);
    });
    
    // إخفاء الاقتراحات عند النقر خارجها
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.account-search, .account-suggestions').length) {
            $('.account-suggestions').hide();
        }
    });
    
    // إخفاء الاقتراحات عند blur
    $(document).on('blur', '.account-search', function() {
        let $input = $(this);
        let $row = $input.closest('tr');
        setTimeout(function() {
            $row.find('.account-suggestions').hide();
        }, 200);
    });
    
    // تحديث العملة
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        $('.line-currency').val(currency);
    });
    
    // إضافة سطر جديد
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        
        let row = `<tr>
            <td>
                <input type="text" class="form-control account-search" placeholder="ابحث عن الحساب..." autocomplete="off">
                <input type="hidden" name="lines[${lineIdx}][account_id]" class="account-id-field" required>
                <div class="account-suggestions" style="display: none;"></div>
            </td>
            <td><input type="text" name="lines[${lineIdx}][description]" class="form-control" placeholder="وصف العملية"></td>
            <td><input type="number" name="lines[${lineIdx}][debit]" class="form-control debit" step="0.01" value="0" min="0" placeholder="0.00"></td>
            <td><input type="number" name="lines[${lineIdx}][credit]" class="form-control credit" step="0.01" value="0" min="0" placeholder="0.00"></td>
            <td>
                <input type="hidden" name="lines[${lineIdx}][currency]" value="${currency}" class="line-currency">
                <input type="hidden" name="lines[${lineIdx}][exchange_rate]" value="1" class="line-exchange-rate">
                <button type="button" class="btn btn-danger btn-sm remove-line" title="حذف السطر">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#linesTable tbody').append(row);
        lineIdx++;
    });
    
    // حذف سطر
    $(document).on('click', '.remove-line', function(){
        if ($('#linesTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('يجب أن يحتوي القيد على سطر واحد على الأقل');
        }
    });
    
    // validation للقيد
    $('#journalForm').on('submit', function(e){
        let debit = 0, credit = 0;
        let hasErrors = false;
        
        console.log('📤 محاولة إرسال النموذج...');
        console.log('🎯 Action URL:', $(this).attr('action'));
        console.log('📋 Method:', $(this).attr('method'));
        
        // طباعة جميع البيانات قبل الإرسال
        let formData = new FormData(this);
        console.log('📦 بيانات النموذج:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
        // فحص جميع السطور
        $('#linesTable tbody tr').each(function(index){
            let accountId = $(this).find('.account-id-field').val();
            let $searchInput = $(this).find('.account-search');
            
            console.log(`📝 السطر ${index + 1}:`, {
                accountId: accountId,
                searchText: $searchInput.val()
            });
            
            if (!accountId) {
                $searchInput.addClass('invalid');
                hasErrors = true;
                console.log('❌ السطر', index + 1, 'بدون حساب مختار');
            } else {
                $searchInput.removeClass('invalid').addClass('selected');
                console.log('✅ السطر', index + 1, 'صحيح');
            }
            
            debit += parseFloat($(this).find('.debit').val()) || 0;
            credit += parseFloat($(this).find('.credit').val()) || 0;
        });
        
        if (hasErrors) {
            alert('❌ يجب اختيار حساب صحيح لجميع السطور');
            e.preventDefault();
            return false;
        }
        
        if (Math.abs(debit - credit) > 0.01) {
            alert(`❌ القيد غير متوازن!\nالمدين: ${debit.toFixed(2)}\nالدائن: ${credit.toFixed(2)}`);
            e.preventDefault();
            return false;
        }
        
        if (debit === 0) {
            alert('❌ يجب إدخال مبالغ في القيد');
            e.preventDefault();
            return false;
        }
        
        console.log('🎉 النموذج صحيح! جاري الإرسال...');
        
        // التأكد من وجود CSRF token
        let csrfToken = $('input[name="_token"]').val();
        console.log('🔐 CSRF Token:', csrfToken);
        
        if (!csrfToken) {
            alert('❌ خطأ في الأمان: CSRF token مفقود');
            e.preventDefault();
            return false;
        }
        
        // إظهار loading
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        
        console.log('🚀 إرسال النموذج الآن...');
        return true;
    });
});
</script>
@endpush