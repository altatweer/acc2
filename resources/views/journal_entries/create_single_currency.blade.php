@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة قيد عملة واحدة</h1>
    <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>الوصف</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="وصف القيد">
                    </div>
                    <div class="form-group col-md-3">
                        <label>العملة</label>
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
            <div class="card-header"><strong>السطور</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="linesTable">
                    <thead>
                        <tr>
                            <th>الحساب</th>
                            <th>الوصف</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>إجراء</th>
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
<style>
/* تحسين أساسي للواجهة */
.account-select {
    font-size: 14px;
    height: 38px;
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: white;
}

.account-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
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
    margin: 2px;
    border-radius: 4px;
}

.btn i {
    margin-left: 5px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function(){
    let lineIdx = $('#linesTable tbody tr').length;
    
    // تحديث العملة في جميع السطور عند تغيير العملة الرئيسية
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        $('.line-currency').val(currency);
    });
    
    // إضافة سطر جديد
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        let accountOptions = `<option value="">-- اختر الحساب --</option>`;
        
        @foreach($accounts as $acc)
            accountOptions += `<option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>`;
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
    
    // validation للقيد قبل الحفظ
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
        
        // إظهار حالة التحميل
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        
        return true;
    });
});
</script>
@endpush