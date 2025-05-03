@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">إضافة راتب جديد</h1>
            <a href="{{ route('salaries.index') }}" class="btn btn-secondary">عودة للقائمة</a>
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
                    <form action="{{ route('salaries.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>الموظف</label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">اختر الموظف</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $employeeId ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->employee_number }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الراتب الأساسي</label>
                            <input type="number" step="0.01" name="basic_salary" class="form-control" required value="{{ old('basic_salary') }}">
                        </div>
                        <div class="form-group">
                            <label>البدلات</label>
                            <div id="allowances-list">
                                <!-- سيتم إضافة عناصر البدلات هنا -->
                            </div>
                            <button type="button" class="btn btn-sm btn-success" onclick="addAllowance()">إضافة بدل</button>
                        </div>
                        <div class="form-group">
                            <label>الخصومات</label>
                            <div id="deductions-list">
                                <!-- سيتم إضافة عناصر الخصومات هنا -->
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="addDeduction()">إضافة خصم</button>
                        </div>
                        <div class="form-group">
                            <label>تاريخ السريان</label>
                            <input type="date" name="effective_from" class="form-control" required value="{{ old('effective_from') }}">
                        </div>
                        <div class="form-group">
                            <label>تاريخ الانتهاء (اختياري)</label>
                            <input type="date" name="effective_to" class="form-control" value="{{ old('effective_to') }}">
                        </div>
                        <button type="submit" class="btn btn-success">حفظ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
function addAllowance() {
    const list = document.getElementById('allowances-list');
    const idx = list.children.length;
    const html = `<div class="row mb-2 allowance-row">
        <div class="col-md-5"><input type="text" name="allowances[${idx}][name]" class="form-control" placeholder="اسم البدل" required></div>
        <div class="col-md-5"><input type="number" step="0.01" name="allowances[${idx}][amount]" class="form-control" placeholder="قيمة البدل" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger" onclick="this.parentNode.parentNode.remove()">حذف</button></div>
    </div>`;
    list.insertAdjacentHTML('beforeend', html);
}
function addDeduction() {
    const list = document.getElementById('deductions-list');
    const idx = list.children.length;
    const html = `<div class="row mb-2 deduction-row">
        <div class="col-md-5"><input type="text" name="deductions[${idx}][name]" class="form-control" placeholder="اسم الخصم" required></div>
        <div class="col-md-5"><input type="number" step="0.01" name="deductions[${idx}][amount]" class="form-control" placeholder="قيمة الخصم" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger" onclick="this.parentNode.parentNode.remove()">حذف</button></div>
    </div>`;
    list.insertAdjacentHTML('beforeend', html);
}
</script>
@endpush
@endsection 