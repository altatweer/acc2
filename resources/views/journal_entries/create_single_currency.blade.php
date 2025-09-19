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
    
    <form action="{{ route('journal-entries.store-single-currency') }}" method="POST">
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
                
                <h5>سطور القيد:</h5>
                
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label>الحساب الأول</label>
                        <select name="lines[0][account_id]" class="form-control" required>
                            <option value="">-- اختر الحساب --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>المبلغ</label>
                        <input type="number" name="lines[0][debit]" class="form-control" step="0.01" min="0" placeholder="المبلغ المدين" required>
                    </div>
                    <div class="col-md-4">
                        <label>الوصف</label>
                        <input type="text" name="lines[0][description]" class="form-control" placeholder="وصف العملية">
                        <input type="hidden" name="lines[0][credit]" value="0">
                        <input type="hidden" name="lines[0][currency]" value="{{ $defaultCurrency }}">
                        <input type="hidden" name="lines[0][exchange_rate]" value="1">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label>الحساب الثاني</label>
                        <select name="lines[1][account_id]" class="form-control" required>
                            <option value="">-- اختر الحساب --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code ? $acc->code . ' - ' . $acc->name : $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>المبلغ</label>
                        <input type="number" name="lines[1][credit]" class="form-control" step="0.01" min="0" placeholder="المبلغ الدائن" required>
                    </div>
                    <div class="col-md-4">
                        <label>الوصف</label>
                        <input type="text" name="lines[1][description]" class="form-control" placeholder="وصف العملية">
                        <input type="hidden" name="lines[1][debit]" value="0">
                        <input type="hidden" name="lines[1][currency]" value="{{ $defaultCurrency }}">
                        <input type="hidden" name="lines[1][exchange_rate]" value="1">
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success btn-lg">
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
</style>
@endpush