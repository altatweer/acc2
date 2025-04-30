@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">إضافة حساب / فئة جديدة</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>اسم الحساب / الفئة</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
<div class="form-group">
    <label>رقم الحساب / الفئة (Code)</label>
    <input type="text" name="code" class="form-control" required>
</div>
                        <div class="form-group">
                            <label>نوع السجل</label>
                            <select name="is_group" id="is_group" class="form-control" onchange="toggleFields()" required>
                                <option value="1">فئة (Group)</option>
                                <option value="0">حساب فعلي (Account)</option>
                            </select>
                        </div>

                        <div id="group_fields">
                            <div class="form-group">
                                <label>نوع الحساب الرئيسي (للفئة)</label>
                                <select name="type" class="form-control">
                                    <option value="">-- اختر --</option>
                                    <option value="asset">أصول</option>
                                    <option value="liability">التزامات</option>
                                    <option value="revenue">إيرادات</option>
                                    <option value="expense">مصاريف</option>
                                    <option value="equity">رأس مال</option>
                                </select>
                            </div>
                        </div>

                        <div id="account_fields" style="display:none;">
                            <div class="form-group">
                                <label>اختر الفئة الرئيسية</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">-- اختر فئة --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>طبيعة الحساب</label>
                                <select name="nature" class="form-control" required>
                                    <option value="debit">مدين</option>
                                    <option value="credit">دائن</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">رجوع</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleFields() {
    var isGroup = document.getElementById('is_group').value;
    if (isGroup == "1") {
        document.getElementById('group_fields').style.display = 'block';
        document.getElementById('account_fields').style.display = 'none';
    } else {
        document.getElementById('group_fields').style.display = 'none';
        document.getElementById('account_fields').style.display = 'block';
    }
}
window.onload = toggleFields;
</script>

@endsection
