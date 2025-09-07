@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">@lang('messages.add_single_currency_entry')</h1>
    <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>@lang('messages.date')</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>@lang('messages.description')</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>@lang('messages.currency')</label>
                        <select name="currency" class="form-control" required>
                            @foreach($currencies as $cur)
                                <option value="{{ $cur->code }}" {{ $cur->code == $defaultCurrency ? 'selected' : '' }}>{{ $cur->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><strong>@lang('messages.lines')</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="linesTable">
                    <thead>
                        <tr>
                            <th>@lang('messages.account')</th>
                            <th>@lang('messages.description')</th>
                            <th>@lang('messages.debit')</th>
                            <th>@lang('messages.credit')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="lines[0][account_id]" class="form-control account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>
                                    @endforeach
                                </select>
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
                                <select name="lines[1][account_id]" class="form-control account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>
                                    @endforeach
                                </select>
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
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
/* تحسينات خاصة لـ Select2 في القيود */
.select2-container {
    font-size: 14px;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.select2-container--bootstrap4 .select2-selection--single {
    text-align: right;
    direction: rtl;
}

.select2-container--bootstrap4 .select2-dropdown {
    direction: rtl;
}

.select2-container--bootstrap4 .select2-results__option {
    text-align: right;
    direction: rtl;
}

.select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
    text-align: right;
    direction: rtl;
}

/* تحسينات إضافية للجدول */
#linesTable th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    position: relative;
}

#linesTable td {
    vertical-align: middle;
    border-color: #dee2e6;
    position: relative;
}

#linesTable .form-control {
    border-radius: 0.375rem;
    border-color: #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

#linesTable .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#linesTable .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
}

#linesTable .btn-sm:hover {
    transform: scale(1.05);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

/* مؤشرات البحث والتحميل */
.select2-container--bootstrap4.select2-container--open .select2-selection {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* تحسين مظهر النتائج */
.select2-results__option {
    border-bottom: 1px solid #f0f0f0;
}

.select2-results__option:last-child {
    border-bottom: none;
}

/* مؤشر التحميل */
.loading-accounts::after {
    content: '🔄 جاري تحميل الحسابات...';
    color: #6c757d;
    font-style: italic;
}

/* تحسين الأزرار */
.btn {
    margin: 0 2px;
}

.btn i {
    margin-right: 5px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// انتظار تحميل Select2 library بالكامل
$(document).ready(function(){
    if (typeof $.fn.select2 === 'undefined') {
        console.error('⚠️ Select2 library not loaded properly');
        return;
    }
    console.log('✅ Select2 library loaded successfully');
});
</script>
<script>
$(document).ready(function(){
    let lineIdx = $('#linesTable tbody tr').length;
    
    // إعدادات Select2 بسيطة
    const select2Options = {
        placeholder: 'اختر الحساب',
        allowClear: true,
        width: '100%',
        theme: 'bootstrap4'
    };
    
    // تطبيق Select2 بشكل آمن
    setTimeout(function(){
        $('.account-select').each(function(){
            if (!$(this).hasClass('select2-hidden-accessible')) {
                try {
                    $(this).select2(select2Options);
                } catch (e) {
                    console.error('Error initializing Select2:', e);
                }
            }
        });
    }, 500);
    
    // تحديث العملة عند التغيير
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        $('.line-currency').val(currency);
    });
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        let row = `<tr>
            <td><select name="lines[${lineIdx}][account_id]" class="form-control account-select" required></select></td>
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
        
        // إضافة الحسابات للسطر الجديد وتطبيق Select2
        let $newSelect = $(`select[name="lines[${lineIdx}][account_id]"]`);
        $newSelect.html(`
            <option value="">-- اختر الحساب --</option>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>
            @endforeach
        `);
        
        // تطبيق Select2 على السطر الجديد مع تأخير بسيط لضمان الاستقرار
        setTimeout(function(){
            $newSelect.select2(select2Options);
        }, 100);
        lineIdx++;
    });
    $(document).on('click', '.remove-line', function(){
        $(this).closest('tr').remove();
    });
    // تحسين validation للقيد
    $('#journalForm').on('submit', function(e){
        let debit = 0, credit = 0;
        let emptyAccounts = 0;
        
        // حساب المجاميع والتحقق من اختيار الحسابات
        $('#linesTable tbody tr').each(function(){
            let accountSelected = $(this).find('select[name*="[account_id]"]').val();
            if (!accountSelected) {
                emptyAccounts++;
            }
            
            debit += parseFloat($(this).find('.debit').val()) || 0;
            credit += parseFloat($(this).find('.credit').val()) || 0;
        });
        
        // التحقق من اختيار الحسابات
        if (emptyAccounts > 0) {
            alert('❌ يجب اختيار حساب لجميع السطور قبل الحفظ');
            e.preventDefault();
            return false;
        }
        
        // التحقق من توازن القيد
        if (Math.abs(debit - credit) > 0.01) {
            alert(`❌ القيد غير متوازن!\n📊 إجمالي المدين: ${debit.toFixed(2)}\n📊 إجمالي الدائن: ${credit.toFixed(2)}\n\n⚖️ يجب أن يكون المدين = الدائن`);
            e.preventDefault();
            return false;
        }
        
        // التحقق من عدم وجود مبالغ صفر
        if (debit === 0 || credit === 0) {
            alert('❌ يجب إدخال مبالغ في المدين والدائن');
            e.preventDefault();
            return false;
        }
        
        // رسالة تأكيد نجاح
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        
        return true;
    });
});
</script>
@endpush 