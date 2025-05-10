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
                            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
                        </tr>
                        <tr>
                            <td><select name="lines[1][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
                            <td><input type="text" name="lines[1][description]" class="form-control"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
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
    let lineIdx = $('#linesTable tbody tr').length;
    let accounts = @json($accounts);
    function filterAccountsByCurrency(currency) {
        $('select[name^="lines"]').each(function(){
            let selected = $(this).val();
            $(this).empty();
            accounts.forEach(function(acc){
                if(acc.currency === currency) {
                    $(this).append(`<option value="${acc.id}" ${selected == acc.id ? 'selected' : ''}>${acc.name}</option>`);
                }
            }.bind(this));
        });
    }
    $('select[name="currency"]').on('change', function(){
        let currency = $(this).val();
        filterAccountsByCurrency(currency);
    });
    // عند التحميل الأولي
    filterAccountsByCurrency($('select[name="currency"]').val());
    $('#addLine').on('click', function(){
        let currency = $('select[name="currency"]').val();
        let row = `<tr>
            <td><select name="lines[${lineIdx}][account_id]" class="form-control" required></select></td>
            <td><input type="text" name="lines[${lineIdx}][description]" class="form-control"></td>
            <td><input type="number" name="lines[${lineIdx}][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
            <td><input type="number" name="lines[${lineIdx}][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
        </tr>`;
        $('#linesTable tbody').append(row);
        filterAccountsByCurrency(currency);
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
    });
});
</script>
@endpush 