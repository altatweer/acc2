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
                                <select name="lines[0][account_id]" class="form-control" required>
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
                                <select name="lines[1][account_id]" class="form-control" required>
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
<style>
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
<script>
let lineIdx = 2;

$(document).ready(function(){
    console.log('🚀 نظام dropdown بسيط - {{ count($accounts) }} حساب متوفر');
    
    // تحديث العملة
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        $('.line-currency').val(currency);
    });
    
    // إضافة سطر جديد
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        
        let accountOptions = '';
        accountOptions += '<option value="">-- اختر الحساب --</option>';
        @foreach($accounts as $acc)
            accountOptions += '<option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . " - " . $acc->name : $acc->name }}</option>';
        @endforeach
        
        let row = `<tr>
            <td>
                <select name="lines[${lineIdx}][account_id]" class="form-control" required>
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
        
        console.log('📤 إرسال النموذج...');
        
        // فحص جميع السطور
        $('#linesTable tbody tr').each(function(index){
            let accountId = $(this).find('select[name*="[account_id]"]').val();
            
            console.log(`السطر ${index + 1}: حساب ${accountId}`);
            
            if (!accountId) {
                hasErrors = true;
                alert(`❌ يجب اختيار حساب للسطر ${index + 1}`);
                return false;
            }
            
            let debitVal = parseFloat($(this).find('.debit').val()) || 0;
            let creditVal = parseFloat($(this).find('.credit').val()) || 0;
            
            debit += debitVal;
            credit += creditVal;
        });
        
        if (hasErrors) {
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
        
        console.log('✅ النموذج صحيح! جاري الحفظ...');
        
        // إظهار loading
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        return true;
    });
});
</script>
@endpush