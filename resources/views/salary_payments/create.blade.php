@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">دفع راتب موظف</h1>
            <a href="{{ route('salary-payments.index') }}" class="btn btn-secondary">عودة للقائمة</a>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('salary-payments.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>الشهر</label>
                            <select name="salary_batch_id" id="salary_batch_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">اختر الشهر</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ $selectedBatchId == $batch->id ? 'selected' : '' }}>{{ $batch->month }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedBatchId && $employees->count() == 0)
                            <div class="alert alert-warning">لا يوجد موظفون لم يستلموا رواتبهم في هذا الشهر.</div>
                        @endif
                        @if($selectedBatchId)
                        <div class="form-group">
                            <label>الموظف</label>
                            <select name="employee_id" id="employee_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">اختر الموظف</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->employee_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if($selectedBatchId && $selectedEmployeeId && !$salary)
                            <div class="alert alert-warning">لا يوجد راتب لهذا الموظف في هذا الشهر أو تم دفعه بالفعل.</div>
                        @endif
                        @if($salary)
                        <div class="alert alert-info">
                            <strong>الراتب الأساسي:</strong> {{ number_format($salary->gross_salary,2) }}<br>
                            <strong>البدلات:</strong> {{ number_format($salary->total_allowances,2) }}<br>
                            <strong>الخصومات:</strong> {{ number_format($salary->total_deductions,2) }}<br>
                            <strong>الصافي:</strong> {{ number_format($salary->net_salary,2) }}<br>
                        </div>
                        <div class="form-group">
                            <label>الصندوق النقدي (مطابق لعملة الموظف)</label>
                            <select name="cash_account_id" class="form-control" required>
                                <option value="">اختر الصندوق</option>
                                @foreach($cashAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->currency }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>تاريخ الدفع</label>
                            <input type="date" name="payment_date" class="form-control" required value="{{ old('payment_date', date('Y-m-d')) }}">
                        </div>
                        <button type="submit" class="btn btn-success">دفع الراتب</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>
<script>
$(function(){
    $('#salary_batch_id, #employee_id').select2({
        width: '100%',
        dir: 'rtl',
        language: 'ar',
        placeholder: 'اختر',
        allowClear: true
    });
});
</script>
@endpush 