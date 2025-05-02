@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">إضافة حساب فعلي جديد</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-info">
                <div class="card-header">
                    <h5 class="card-title">بيانات الحساب</h5>
                </div>
                <form action="{{ route('accounts.storeAccount') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">اسم الحساب</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>رمز الحساب</label>
                                <input type="text" id="code" class="form-control" value="{{ $nextCode }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $nextCode }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">اختر الفئة الرئيسية</label>
                                <select id="parent_id" name="parent_id" class="form-control" required>
                                    <option value="">-- اختر الفئة --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">طبيعة الحساب</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- اختر نوع --</option>
                                    <option value="debit" {{ old('nature')=='debit' ? 'selected' : '' }}>مدين</option>
                                    <option value="credit" {{ old('nature')=='credit' ? 'selected' : '' }}>دائن</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <!-- ensure value is always sent: 0 if unchecked -->
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" name="is_cash_box" value="1" class="form-check-input" id="cashBoxCheck"
                                {{ old('is_cash_box') ? 'checked' : '' }}>
                            <label class="form-check-label" for="cashBoxCheck">هل هو صندوق نقدي؟</label>
                        </div>

                        <div class="form-group">
                            <label for="currency">عملة الحساب</label>
                            <select name="currency" id="currency" class="form-control select2" required>
                                <option value="" disabled {{ old('currency') ? '' : 'selected' }}>-- اختر العملة --</option>
                                @foreach($currencies as $cur)
                                    <option value="{{ $cur->code }}" {{ old('currency') == $cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
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
                        <button type="submit" class="btn btn-success">حفظ</button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">إلغاء</a>
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
    refreshAccountCode();
});
</script>
@endpush
