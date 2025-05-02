@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة قيد محاسبي يدوي</h1>
    <form action="{{ route('journal-entries.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-9">
                        <label>الوصف العام</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><strong>تفاصيل السطور</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="linesTable">
                    <thead>
                        <tr>
                            <th>الحساب</th>
                            <th>الوصف</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>العملة</th>
                            <th>سعر الصرف</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('lines'))
                            @foreach(old('lines') as $i => $line)
                                <tr>
                                    <td><select name="lines[{{ $i }}][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}" {{ $line['account_id']==$acc->id?'selected':'' }}>{{ $acc->name }}</option>@endforeach</select></td>
                                    <td><input type="text" name="lines[{{ $i }}][description]" class="form-control" value="{{ $line['description'] ?? '' }}"></td>
                                    <td><input type="number" name="lines[{{ $i }}][debit]" class="form-control debit" step="0.01" value="{{ $line['debit'] ?? 0 }}" min="0"></td>
                                    <td><input type="number" name="lines[{{ $i }}][credit]" class="form-control credit" step="0.01" value="{{ $line['credit'] ?? 0 }}" min="0"></td>
                                    <td><input type="text" name="lines[{{ $i }}][currency]" class="form-control" value="{{ $line['currency'] ?? 'IQD' }}" required></td>
                                    <td><input type="number" name="lines[{{ $i }}][exchange_rate]" class="form-control" step="0.000001" value="{{ $line['exchange_rate'] ?? 1 }}" required></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
                                </tr>
                            @endforeach
                        @else
                        <tr>
                            <td><select name="lines[0][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
                            <td><input type="text" name="lines[0][description]" class="form-control"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
                            <td><input type="text" name="lines[0][currency]" class="form-control" value="IQD" required></td>
                            <td><input type="number" name="lines[0][exchange_rate]" class="form-control" step="0.000001" value="1" required></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
                        </tr>
                        <tr>
                            <td><select name="lines[1][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
                            <td><input type="text" name="lines[1][description]" class="form-control"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
                            <td><input type="text" name="lines[1][currency]" class="form-control" value="IQD" required></td>
                            <td><input type="number" name="lines[1][exchange_rate]" class="form-control" step="0.000001" value="1" required></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-primary" id="addLine">إضافة سطر</button>
                <button type="submit" class="btn btn-success">حفظ القيد</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    let lineIdx = $('#linesTable tbody tr').length;
    $('#addLine').on('click', function(){
        let row = `<tr>
            <td><select name="lines[${lineIdx}][account_id]" class="form-control" required>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></td>
            <td><input type="text" name="lines[${lineIdx}][description]" class="form-control"></td>
            <td><input type="number" name="lines[${lineIdx}][debit]" class="form-control debit" step="0.01" value="0" min="0"></td>
            <td><input type="number" name="lines[${lineIdx}][credit]" class="form-control credit" step="0.01" value="0" min="0"></td>
            <td><input type="text" name="lines[${lineIdx}][currency]" class="form-control" value="IQD" required></td>
            <td><input type="number" name="lines[${lineIdx}][exchange_rate]" class="form-control" step="0.000001" value="1" required></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-line">✖</button></td>
        </tr>`;
        $('#linesTable tbody').append(row);
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
            alert('يجب أن يتساوى مجموع المدين مع مجموع الدائن!');
            return false;
        }
    });
});
</script>
@endpush 