@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>@lang('messages.add_main_sub_category')</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>@lang('messages.validation_errors')</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('accounts.storeGroup') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>@lang('messages.category_name')</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>@lang('messages.category_code')</label>
                    <input type="text" id="groupCode" class="form-control" value="{{ $nextCode }}" disabled>
                    <input type="hidden" id="groupCodeInput" name="code" value="{{ $nextCode }}">
                </div>

                <div class="form-group">
                    <label>@lang('messages.account_type')</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">@lang('messages.select_option')</option>
                        <option value="asset">@lang('messages.type_asset')</option>
                        <option value="liability">@lang('messages.type_liability')</option>
                        <option value="revenue">@lang('messages.type_revenue')</option>
                        <option value="expense">@lang('messages.type_expense')</option>
                        <option value="equity">@lang('messages.type_equity')</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('messages.parent_category_optional')</label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        <option value="">@lang('messages.none_option')</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">@lang('messages.save')</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">@lang('messages.cancel')</a>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function refreshGroupCode(){
        let isGroup = 1;
        let parentId = $('#parent_id').val() || '';
        let typeVal = $('#type').val() || '';
        
        // تنفيذ طلب AJAX فقط إذا تم اختيار نوع الحساب على الأقل
        if (typeVal) {
            // إضافة معلمة عشوائية لمنع التخزين المؤقت
            $.getJSON("{{ route('accounts.nextCode') }}?_=" + new Date().getTime(), 
                { is_group: isGroup, parent_id: parentId, type: typeVal }, 
                function(data){
                    if (data.nextCode) {
                        $('#groupCode').val(data.nextCode);
                        $('#groupCodeInput').val(data.nextCode);
                        console.log("تم تحديث كود الفئة إلى: " + data.nextCode); // سجل للتصحيح
                    } else {
                        console.warn("تم استلام استجابة من الخادم لكن بدون كود"); // سجل للتصحيح
                    }
                }
            ).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("خطأ في الحصول على كود الفئة: " + textStatus, errorThrown);
            });
        } else {
            // إذا لم يتم اختيار نوع، عرض رسالة إرشادية بالعربية
            $('#groupCode').val("اختر نوع الحساب أولاً");
            $('#groupCodeInput').val("");
        }
    }
    
    // تنفيذ عند تغيير الاختيارات
    $('#type, #parent_id').on('change', refreshGroupCode);
    
    // التأكد من تحديث الكود عند تحميل الصفحة إذا كانت هناك قيم محددة مسبقاً
    setTimeout(function() {
        if ($('#type').val()) {
            refreshGroupCode();
        }
    }, 500); // تأخير قصير للتأكد من تحميل الصفحة بالكامل
});
</script>
@endpush
