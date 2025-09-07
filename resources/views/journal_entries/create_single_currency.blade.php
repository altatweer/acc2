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
                            <td><select name="lines[0][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
                            <td><input type="text" name="lines[0][description]" class="form-control"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
                            <td>
                                <input type="hidden" name="lines[0][currency]" value="{{ old('currency', $defaultCurrency) }}" class="line-currency">
                                <input type="hidden" name="lines[0][exchange_rate]" value="1" class="line-exchange-rate">
                                <button type="button" class="btn btn-danger btn-sm remove-line">✖</button>
                            </td>
                        </tr>
                        <tr>
                            <td><select name="lines[1][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
                            <td><input type="text" name="lines[1][description]" class="form-control"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
                            <td>
                                <input type="hidden" name="lines[1][currency]" value="{{ old('currency', $defaultCurrency) }}" class="line-currency">
                                <input type="hidden" name="lines[1][exchange_rate]" value="1" class="line-exchange-rate">
                                <button type="button" class="btn btn-danger btn-sm remove-line">✖</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-primary" id="addLine">@lang('messages.add_line')</button>
                <button type="submit" class="btn btn-success">@lang('messages.save_entry')</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    let lineIdx = $('#linesTable tbody tr').length;
    let accounts = @json($accounts);
    function loadAllAccounts() {
        // في القيد أحادي العملة، نعرض جميع الحسابات بدون فلترة
        $('select[name^="lines"]').each(function(){
            let selected = $(this).val();
            
            // Destroy Select2 if it's initialized
            if ($.fn.select2 && $(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            $(this).empty();
            // إضافة خيار فارغ أولاً
            $(this).append(`<option value="">-- اختر الحساب --</option>`);
            
            accounts.forEach(function(acc){
                // عرض رمز الحساب مع اسم الحساب لوضوح أكبر
                let displayName = acc.code ? `${acc.code} - ${acc.name}` : acc.name;
                $(this).append(`<option value="${acc.id}" ${selected == acc.id ? 'selected' : ''}>${displayName}</option>`);
            }.bind(this));
            
            // Reinitialize Select2 if needed
            if ($.fn.select2) {
                $(this).select2({
                    placeholder: 'اختر الحساب',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
        
        // تحديث حقول العملة وسعر الصرف في كل سطر
        let currency = $('select[name="currency"]').val();
        $('.line-currency').val(currency);
        $('.line-exchange-rate').val(1);
    }
    $('select[name="currency"]').on('change', function(){
        // في القيد أحادي العملة، فقط نحديث قيم العملة في الحقول المخفية
        let currency = $(this).val();
        $('.line-currency').val(currency);
    });
    
    // عند التحميل الأولي - تحميل جميع الحسابات
    loadAllAccounts();
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        let row = `<tr>
            <td><select name="lines[${lineIdx}][account_id]" class="form-control" required></select></td>
            <td><input type="text" name="lines[${lineIdx}][description]" class="form-control"></td>
            <td><input type="number" name="lines[${lineIdx}][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
            <td><input type="number" name="lines[${lineIdx}][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
            <td>
                <input type="hidden" name="lines[${lineIdx}][currency]" value="${currency}" class="line-currency">
                <input type="hidden" name="lines[${lineIdx}][exchange_rate]" value="1" class="line-exchange-rate">
                <button type="button" class="btn btn-danger btn-sm remove-line">✖</button>
            </td>
        </tr>`;
        $('#linesTable tbody').append(row);
        loadAllAccounts(); // إعادة تحميل جميع الحسابات للسطور الجديدة
        lineIdx++;
    });
    $(document).on('click', '.remove-line', function(){
        $(this).closest('tr').remove();
    });
    // تحقق من توازن القيد قبل الإرسال
    $('#journalForm').on('submit', function(){
        let debit = 0, credit = 0;
        $('.debit').each(function(){ debit += parseFloat($(this).val())||0; });
        $('.credit').each(function(){ credit += parseFloat($(this).val())||0; });
        if (debit.toFixed(2) !== credit.toFixed(2)) {
            alert("{{ __('messages.debit_credit_must_equal') }}");
            return false;
        }
        // تحقق من عدم تكرار نفس الحساب في المدين والدائن
        let accounts = [];
        let duplicate = false;
        $('#linesTable tbody tr').each(function(){
            let acc = $(this).find('select[name*="[account_id]"]').val();
            if(accounts.includes(acc)) {
                duplicate = true;
            } else {
                accounts.push(acc);
            }
        });
        if(duplicate) {
            alert('لا يمكن اختيار نفس الحساب في المدين والدائن. يرجى اختيار حسابات مختلفة.');
            return false;
        }
    });
});
</script>
@endpush 