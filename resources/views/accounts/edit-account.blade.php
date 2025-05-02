@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">تعديل حساب فعلي</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning">
                <div class="card-header">
                    <h5 class="card-title">بيانات الحساب</h5>
                </div>
                <form action="{{ route('accounts.update', $account) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <input type="hidden" name="is_group" value="0">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">اسم الحساب</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $account->name) }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>رمز الحساب</label>
                                <input type="text" id="code" class="form-control" value="{{ $account->code }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $account->code }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">اختر الفئة الرئيسية</label>
                                <select id="parent_id" name="parent_id" class="form-control" required>
                                    <option value="">-- اختر الفئة --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id', $account->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">طبيعة الحساب</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- اختر --</option>
                                    <option value="debit" {{ old('nature', $account->nature) == 'debit' ? 'selected' : '' }}>مدين</option>
                                    <option value="credit" {{ old('nature', $account->nature) == 'credit' ? 'selected' : '' }}>دائن</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" id="isCashBox" name="is_cash_box" value="1" class="form-check-input" {{ old('is_cash_box', $account->is_cash_box) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isCashBox">هذا صندوق نقدي</label>
                        </div>

                        <div class="form-group">
                            <label for="currency">عملة الحساب</label>
                            <select name="currency" id="currency" class="form-control" required>
                                <option value="" disabled {{ old('currency', $account->currency) ? '' : 'selected' }}>-- اختر العملة --</option>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency', $account->currency) == $cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> تحديث
                        </button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function refreshAccountCode(){
        let isGroup = 0;
        let parent_id = $('#parent_id').val() || '';
        $.getJSON("{{ route('accounts.nextCode') }}", { is_group: isGroup, parent_id: parent_id }, function(data){
            $('#code').val(data.nextCode);
            $('#codeInput').val(data.nextCode);
        });
    }
    $('#parent_id').on('change', refreshAccountCode);
});
</script>
@endpush
