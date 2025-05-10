@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1 class="mb-4">@lang('messages.add_multi_currency_entry')</h1>
    <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>@lang('messages.date')</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-9">
                        <label>@lang('messages.description')</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
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
                            <th>@lang('messages.currency')</th>
                            <th>@lang('messages.exchange_rate')</th>
                            <th>@lang('messages.converted_amount_iqd')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="lines[0][account_id]" class="form-control account-select" data-row="0" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}" {{ old('lines.0.account_id', $accounts[0]->id ?? null) == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[0][description]" class="form-control" value="{{ old('lines.0.description') }}"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control debit amount-input" step="0.01" value="{{ old('lines.0.debit', 0) }}" min="0" data-row="0"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control credit amount-input" step="0.01" value="{{ old('lines.0.credit', 0) }}" min="0" data-row="0"></td>
                            <td><input type="text" name="lines[0][currency]" class="form-control currency-input" value="{{ old('lines.0.currency', $accounts[0]->currency ?? 'IQD') }}" readonly data-row="0"></td>
                            <td><input type="number" name="lines[0][exchange_rate]" class="form-control exchange-rate-input" step="0.000001" value="{{ old('lines.0.exchange_rate', 1) }}" required data-row="0"></td>
                            <td><input type="text" class="form-control converted-amount" value="0" readonly data-row="0"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="lines[1][account_id]" class="form-control account-select" data-row="1" required>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-currency="{{ $acc->currency }}" {{ old('lines.1.account_id', $accounts[1]->id ?? null) == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[1][description]" class="form-control" value="{{ old('lines.1.description') }}"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit amount-input" step="0.01" value="{{ old('lines.1.debit', 0) }}" min="0" data-row="1"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit amount-input" step="0.01" value="{{ old('lines.1.credit', 0) }}" min="0" data-row="1"></td>
                            <td><input type="text" name="lines[1][currency]" class="form-control currency-input" value="{{ old('lines.1.currency', $accounts[1]->currency ?? 'IQD') }}" readonly data-row="1"></td>
                            <td><input type="number" name="lines[1][exchange_rate]" class="form-control exchange-rate-input" step="0.000001" value="{{ old('lines.1.exchange_rate', 1) }}" required data-row="1"></td>
                            <td><input type="text" class="form-control converted-amount" value="0" readonly data-row="1"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
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

@push('scripts')
<script>
$(function(){
    let accounts = @json($accounts);
    let lineIdx = $('#linesTable tbody tr').length;
    function updateCurrency(row) {
        let accountId = $(`select.account-select[data-row='${row}']`).val();
        let acc = accounts.find(a => a.id == accountId);
        let currency = acc ? acc.currency : 'IQD';
        $(`input.currency-input[data-row='${row}']`).val(currency);
    }
    function updateConverted(row) {
        let currency = $(`input.currency-input[data-row='${row}']`).val();
        let debit = parseFloat($(`input.debit[data-row='${row}']`).val())||0;
        let credit = parseFloat($(`input.credit[data-row='${row}']`).val())||0;
        let amount = debit > 0 ? debit : credit;
        let rate = parseFloat($(`input.exchange-rate-input[data-row='${row}']`).val())||1;
        let converted = (currency === 'IQD') ? amount : amount * rate;
        $(`input.converted-amount[data-row='${row}']`).val(converted.toFixed(2));
    }
    $('#addLine').on('click', function(){
        let row = `<tr>
            <td><select name="lines[${lineIdx}][account_id]" class="form-control account-select" data-row="${lineIdx}" required>`;
        accounts.forEach(function(acc){
            row += `<option value="${acc.id}" data-currency="${acc.currency}">${acc.name}</option>`;
        });
        row += `</select></td>
            <td><input type="text" name="lines[${lineIdx}][description]" class="form-control"></td>
            <td><input type="number" name="lines[${lineIdx}][debit]" class="form-control debit amount-input" step="0.01" value="0" min="0" data-row="${lineIdx}"></td>
            <td><input type="number" name="lines[${lineIdx}][credit]" class="form-control credit amount-input" step="0.01" value="0" min="0" data-row="${lineIdx}"></td>
            <td><input type="text" name="lines[${lineIdx}][currency]" class="form-control currency-input" value="${accounts[0].currency}" readonly data-row="${lineIdx}"></td>
            <td><input type="number" name="lines[${lineIdx}][exchange_rate]" class="form-control exchange-rate-input" step="0.000001" value="1" required data-row="${lineIdx}"></td>
            <td><input type="text" class="form-control converted-amount" value="0" readonly data-row="${lineIdx}"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
        </tr>`;
        $('#linesTable tbody').append(row);
        lineIdx++;
    });
    $(document).on('change', 'select.account-select', function(){
        let row = $(this).data('row');
        updateCurrency(row);
        updateConverted(row);
    });
    $(document).on('input', '.amount-input, .exchange-rate-input', function(){
        let row = $(this).data('row');
        updateConverted(row);
    });
    $(document).on('click', '.remove-line', function(){
        $(this).closest('tr').remove();
    });
    // تحقق من توازن القيد بعد التحويل
    $('#journalForm').on('submit', function(){
        let totalDebit = 0, totalCredit = 0;
        $('input.debit').each(function(){
            let row = $(this).data('row');
            let val = parseFloat($(this).val())||0;
            let conv = parseFloat($(`input.converted-amount[data-row='${row}']`).val())||0;
            if(val > 0) totalDebit += conv;
        });
        $('input.credit').each(function(){
            let row = $(this).data('row');
            let val = parseFloat($(this).val())||0;
            let conv = parseFloat($(`input.converted-amount[data-row='${row}']`).val())||0;
            if(val > 0) totalCredit += conv;
        });
        if (totalDebit.toFixed(2) !== totalCredit.toFixed(2)) {
            alert("{{ __('messages.debit_credit_must_equal_after_conversion') }}");
            return false;
        }
    });
    // تهيئة القيم الافتراضية عند التحميل
    $('#linesTable tbody tr').each(function(){
        let row = $(this).find('select.account-select').data('row');
        updateCurrency(row);
        updateConverted(row);
    });
});
</script>
@endpush 