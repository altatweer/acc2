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
                                <select name="lines[0][account_id]" class="form-control account-select" required>
                                    <option value="">-- ابحث واختر الحساب --</option>
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
                                    <option value="">-- ابحث واختر الحساب --</option>
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
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #495057;
    line-height: 1.5;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.75rem);
}

/* RTL Support */
.select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
    text-align: right;
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let lineIdx = 2;

$(document).ready(function(){
    console.log('🚀 بدء التحميل - {{ count($accounts) }} حساب متوفر');
    
    // تهيئة Select2 للحسابات الموجودة
    $('.account-select').select2({
        placeholder: '-- ابحث واختر الحساب --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return 'لا توجد نتائج';
            },
            searching: function() {
                return 'جاري البحث...';
            }
        }
    });
    
    console.log('✅ تم تهيئة Select2 للحسابات');
    
    // تحديث العملة
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        $('.line-currency').val(currency);
        console.log('💱 تحديث العملة إلى:', currency);
    });
    
    // إضافة سطر جديد
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        
        let accountOptions = '';
        accountOptions += '<option value="">-- ابحث واختر الحساب --</option>';
        @foreach($accounts as $acc)
            accountOptions += '<option value="{{ $acc->id }}">{{ addslashes($acc->code ? $acc->code . " - " . $acc->name : $acc->name) }}</option>';
        @endforeach
        
        let row = `<tr>
            <td>
                <select name="lines[${lineIdx}][account_id]" class="form-control account-select" required>
                    ${accountOptions}
                </select>
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
        
        // تهيئة Select2 للسطر الجديد
        $(`select[name="lines[${lineIdx}][account_id]"]`).select2({
            placeholder: '-- ابحث واختر الحساب --',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return 'لا توجد نتائج';
                },
                searching: function() {
                    return 'جاري البحث...';
                }
            }
        });
        
        lineIdx++;
        console.log('➕ تمت إضافة سطر جديد رقم', lineIdx-1);
    });
    
    // حذف سطر
    $(document).on('click', '.remove-line', function(){
        if ($('#linesTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            console.log('🗑️ تم حذف سطر');
        } else {
            alert('يجب أن يحتوي القيد على سطر واحد على الأقل');
        }
    });
    
    // validation شامل مع debugging قوي
    $('#journalForm').on('submit', function(e){
        console.log('🔥 بداية عملية الإرسال!');
        console.log('📍 URL:', window.location.href);
        console.log('🎯 Action:', $(this).attr('action'));
        console.log('📋 Method:', $(this).attr('method'));
        
        let debit = 0, credit = 0;
        let hasErrors = false;
        let formData = {};
        
        // جمع بيانات النموذج الأساسية
        formData.currency = $('select[name="currency"]').val();
        formData.date = $('input[name="date"]').val();
        formData.description = $('input[name="description"]').val();
        formData._token = $('input[name="_token"]').val();
        formData.lines = [];
        
        console.log('📦 البيانات الأساسية:', formData);
        
        // فحص جميع السطور
        $('#linesTable tbody tr').each(function(index){
            let accountId = $(this).find('select[name*="[account_id]"]').val();
            let description = $(this).find('input[name*="[description]"]').val();
            let debitVal = parseFloat($(this).find('.debit').val()) || 0;
            let creditVal = parseFloat($(this).find('.credit').val()) || 0;
            let currency = $(this).find('.line-currency').val();
            let exchangeRate = $(this).find('.line-exchange-rate').val();
            
            let lineData = {
                account_id: accountId,
                description: description,
                debit: debitVal,
                credit: creditVal,
                currency: currency,
                exchange_rate: exchangeRate
            };
            
            formData.lines.push(lineData);
            
            console.log(`📝 السطر ${index + 1}:`, lineData);
            
            if (!accountId) {
                hasErrors = true;
                console.error(`❌ السطر ${index + 1}: لا يوجد حساب مختار`);
                alert(`❌ يجب اختيار حساب للسطر ${index + 1}`);
                return false;
            }
            
            debit += debitVal;
            credit += creditVal;
        });
        
        console.log('💰 إجمالي المدين:', debit);
        console.log('💰 إجمالي الدائن:', credit);
        console.log('⚖️ الفرق:', Math.abs(debit - credit));
        
        if (hasErrors) {
            console.error('❌ يوجد أخطاء في النموذج');
            e.preventDefault();
            return false;
        }
        
        if (Math.abs(debit - credit) > 0.01) {
            console.error('❌ القيد غير متوازن');
            alert(`❌ القيد غير متوازن!\nالمدين: ${debit.toFixed(2)}\nالدائن: ${credit.toFixed(2)}`);
            e.preventDefault();
            return false;
        }
        
        if (debit === 0) {
            console.error('❌ لا توجد مبالغ');
            alert('❌ يجب إدخال مبالغ في القيد');
            e.preventDefault();
            return false;
        }
        
        console.log('✅ جميع الفحوصات تمت بنجاح!');
        console.log('📤 البيانات الكاملة للإرسال:', formData);
        
        // طباعة HTML للنموذج
        console.log('🔍 HTML النموذج:', $(this)[0].outerHTML.substring(0, 500) + '...');
        
        // إظهار loading
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        
        console.log('🚀 إرسال النموذج الآن!');
        
        // لا نوقف الإرسال - دع Laravel يتولى الأمر
        return true;
    });
    
    console.log('🎉 تم تحميل جميع الـ JavaScript بنجاح');
});
</script>
@endpush