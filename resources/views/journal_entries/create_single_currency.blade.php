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
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
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
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="linesTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 30%">الحساب</th>
                                <th style="width: 25%">الوصف</th>
                                <th style="width: 15%">مدين</th>
                                <th style="width: 15%">دائن</th>
                                <th style="width: 15%">إجراءات</th>
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
                                <td>
                                    <input type="text" name="lines[0][description]" class="form-control" placeholder="وصف العملية">
                                </td>
                                <td>
                                    <input type="number" name="lines[0][debit]" class="form-control debit" step="0.01" min="0" placeholder="0.00" value="0">
                                </td>
                                <td>
                                    <input type="number" name="lines[0][credit]" class="form-control credit" step="0.01" min="0" placeholder="0.00" value="0">
                                </td>
                                <td>
                                    <input type="hidden" name="lines[0][currency]" value="{{ $defaultCurrency }}">
                                    <input type="hidden" name="lines[0][exchange_rate]" value="1">
                                    <button type="button" class="btn btn-danger btn-sm remove-line" disabled>
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
                                <td>
                                    <input type="text" name="lines[1][description]" class="form-control" placeholder="وصف العملية">
                                </td>
                                <td>
                                    <input type="number" name="lines[1][debit]" class="form-control debit" step="0.01" min="0" placeholder="0.00" value="0">
                                </td>
                                <td>
                                    <input type="number" name="lines[1][credit]" class="form-control credit" step="0.01" min="0" placeholder="0.00" value="0">
                                </td>
                                <td>
                                    <input type="hidden" name="lines[1][currency]" value="{{ $defaultCurrency }}">
                                    <input type="hidden" name="lines[1][exchange_rate]" value="1">
                                    <button type="button" class="btn btn-danger btn-sm remove-line">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="2" class="text-right">الإجمالي:</th>
                                <th id="totalDebit" class="text-center">0.00</th>
                                <th id="totalCredit" class="text-center">0.00</th>
                                <th id="difference" class="text-center">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary" id="addLine">
                            <i class="fas fa-plus"></i> إضافة سطر جديد
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> حفظ القيد
                        </button>
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
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

.alert {
    border-radius: 4px;
}

label {
    font-weight: 600;
    margin-bottom: 5px;
}

.thead-light th {
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-align: center;
    font-weight: 600;
}

.table td, .table th {
    vertical-align: middle;
    border: 1px solid #dee2e6;
}

.table-info th {
    background-color: #d1ecf1;
    font-weight: bold;
}

#difference.balanced {
    color: green;
    background-color: #d4edda;
}

#difference.unbalanced {
    color: red;
    background-color: #f8d7da;
}
</style>
@endpush

@push('scripts')
<script>
let lineIndex = 2;

$(document).ready(function(){
    
    // تحديث الإجماليات
    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        
        $('.debit').each(function() {
            totalDebit += parseFloat($(this).val()) || 0;
        });
        
        $('.credit').each(function() {
            totalCredit += parseFloat($(this).val()) || 0;
        });
        
        let difference = totalDebit - totalCredit;
        
        $('#totalDebit').text(totalDebit.toFixed(2));
        $('#totalCredit').text(totalCredit.toFixed(2));
        $('#difference').text(Math.abs(difference).toFixed(2));
        
        if (Math.abs(difference) < 0.01) {
            $('#difference').removeClass('unbalanced').addClass('balanced');
        } else {
            $('#difference').removeClass('balanced').addClass('unbalanced');
        }
    }
    
    // تحديث العملة للسطور
    function updateCurrency() {
        let currency = $('select[name="currency"]').val();
        $('.line-currency').val(currency);
    }
    
    // إضافة سطر جديد
    $('#addLine').on('click', function() {
        let currency = $('select[name="currency"]').val();
        
        let row = `
        <tr>
            <td>
                <select name="lines[${lineIndex}][account_id]" class="form-control" required>
                    <option value="">-- اختر الحساب --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ addslashes($acc->code ? $acc->code . ' - ' . $acc->name : $acc->name) }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="lines[${lineIndex}][description]" class="form-control" placeholder="وصف العملية">
            </td>
            <td>
                <input type="number" name="lines[${lineIndex}][debit]" class="form-control debit" step="0.01" min="0" placeholder="0.00" value="0">
            </td>
            <td>
                <input type="number" name="lines[${lineIndex}][credit]" class="form-control credit" step="0.01" min="0" placeholder="0.00" value="0">
            </td>
            <td>
                <input type="hidden" name="lines[${lineIndex}][currency]" value="${currency}" class="line-currency">
                <input type="hidden" name="lines[${lineIndex}][exchange_rate]" value="1">
                <button type="button" class="btn btn-danger btn-sm remove-line">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
        
        $('#linesTable tbody').append(row);
        lineIndex++;
        updateRemoveButtons();
        updateTotals();
    });
    
    // حذف سطر
    $(document).on('click', '.remove-line', function() {
        if ($('#linesTable tbody tr').length > 2) {
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
    
    // تحديث العملة عند التغيير
    $('select[name="currency"]').on('change', updateCurrency);
    
    // تحديث الإجماليات عند تغيير المبالغ
    $(document).on('input', '.debit, .credit', updateTotals);
    
    // التحقق من القيد قبل الإرسال
    $('#journalForm').on('submit', function(e) {
        let totalDebit = parseFloat($('#totalDebit').text()) || 0;
        let totalCredit = parseFloat($('#totalCredit').text()) || 0;
        let difference = Math.abs(totalDebit - totalCredit);
        
        // فحص التوازن
        if (difference > 0.01) {
            e.preventDefault();
            alert(`❌ القيد غير متوازن!\nالمدين: ${totalDebit.toFixed(2)}\nالدائن: ${totalCredit.toFixed(2)}\nالفرق: ${difference.toFixed(2)}`);
            return false;
        }
        
        // فحص وجود مبالغ
        if (totalDebit === 0 && totalCredit === 0) {
            e.preventDefault();
            alert('❌ يجب إدخال مبالغ في القيد');
            return false;
        }
        
        // فحص اختيار الحسابات
        let missingAccounts = false;
        $('select[name*="[account_id]"]').each(function() {
            if (!$(this).val()) {
                missingAccounts = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (missingAccounts) {
            e.preventDefault();
            alert('❌ يجب اختيار حساب لجميع السطور');
            return false;
        }
        
        // إظهار loading
        $(this).find('button[type="submit"]').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
    });
    
    // تهيئة أولى
    updateTotals();
    updateRemoveButtons();
});
</script>
@endpush