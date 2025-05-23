@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3 class="m-0">@lang('messages.add_new_real_account')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-info">
                <div class="card-header">
                    <h5 class="card-title">@lang('messages.real_account_data')</h5>
                </div>
                <form action="{{ route('accounts.storeAccount') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">@lang('messages.account_name')</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('messages.account_code')</label>
                                <input type="text" id="code" class="form-control" value="{{ $nextCode }}" disabled>
                                <input type="hidden" id="codeInput" name="code" value="{{ $nextCode }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="parent_id">@lang('messages.select_parent_category')</label>
                                <select id="parent_id" name="parent_id" class="form-control" required>
                                    <option value="">-- @lang('messages.select_category') --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nature">@lang('messages.account_nature')</label>
                                <select id="nature" name="nature" class="form-control" required>
                                    <option value="">-- @lang('messages.select_account_nature') --</option>
                                    <option value="debit" {{ old('nature')=='debit' ? 'selected' : '' }}>@lang('messages.debit_nature')</option>
                                    <option value="credit" {{ old('nature')=='credit' ? 'selected' : '' }}>@lang('messages.credit_nature')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <!-- ensure value is always sent: 0 if unchecked -->
                            <input type="hidden" name="is_cash_box" value="0">
                            <input type="checkbox" name="is_cash_box" value="1" class="form-check-input" id="cashBoxCheck"
                                {{ old('is_cash_box') ? 'checked' : '' }}>
                            <label class="form-check-label" for="cashBoxCheck">@lang('messages.is_cash_box')</label>
                        </div>

                        <div class="form-group">
                            <label for="currency">@lang('messages.account_currency')</label>
                            <select name="currency" id="currency" class="form-control select2" required>
                                <option value="" disabled {{ old('currency') ? '' : 'selected' }}>-- @lang('messages.select_currency') --</option>
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
                        <button type="submit" class="btn btn-success">@lang('messages.save')</button>
                        <a href="{{ route('accounts.real') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
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
        
        // تنفيذ طلب AJAX فقط إذا تم اختيار فئة أب
        if (parent_id) {
            // إضافة معلمة عشوائية لمنع التخزين المؤقت
            $.getJSON("{{ route('accounts.nextCode') }}?_=" + new Date().getTime(), 
                { is_group: isGroup, parent_id: parent_id }, 
                function(data){
                    if (data.nextCode) {
                        $('#code').val(data.nextCode);
                        $('#codeInput').val(data.nextCode);
                        console.log("تم تحديث الكود إلى: " + data.nextCode); // سجل للتصحيح
                    } else {
                        console.warn("تم استلام استجابة من الخادم لكن بدون كود"); // سجل للتصحيح
                    }
                }
            ).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("خطأ في الحصول على الكود: " + textStatus, errorThrown);
            });
        } else {
            // إذا لم يتم اختيار فئة أب، عرض رسالة إرشادية بالعربية
            $('#code').val("اختر فئة أولاً");
            $('#codeInput').val("");
        }
    }
    
    // تنفيذ عند تغيير الاختيار
    $('#parent_id').on('change', refreshAccountCode);
    
    // التأكد من تحديث الكود عند تحميل الصفحة إذا كانت هناك قيمة محددة مسبقاً
    setTimeout(function() {
        if ($('#parent_id').val()) {
            refreshAccountCode();
        }
    }, 500); // تأخير قصير للتأكد من تحميل الصفحة بالكامل
});
</script>
@endpush
