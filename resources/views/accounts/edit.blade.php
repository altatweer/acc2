@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">تعديل حساب / فئة</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('accounts.update', $account->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>اسم الحساب / الفئة</label>
                            <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                        </div>
<div class="form-group">
    <label>رقم الحساب / الفئة (Code)</label>
    <input type="text" name="code" value="{{ $account->code }}" class="form-control" required>
</div>

                        <div class="form-group">
                            <label>نوع السجل</label>
                            <select name="is_group" id="is_group" class="form-control" onchange="toggleFields()" required>
                                <option value="1" {{ $account->is_group ? 'selected' : '' }}>فئة (Group)</option>
                                <option value="0" {{ !$account->is_group ? 'selected' : '' }}>حساب فعلي (Account)</option>
                            </select>
                        </div>

                        <div id="group_fields">
                            <div class="form-group">
                                <label>نوع الحساب الرئيسي (للفئة)</label>
                                <select name="type" class="form-control">
                                    <option value="">-- اختر --</option>
                                    <option value="asset" {{ $account->type == 'asset' ? 'selected' : '' }}>أصول</option>
                                    <option value="liability" {{ $account->type == 'liability' ? 'selected' : '' }}>التزامات</option>
                                    <option value="revenue" {{ $account->type == 'revenue' ? 'selected' : '' }}>إيرادات</option>
                                    <option value="expense" {{ $account->type == 'expense' ? 'selected' : '' }}>مصاريف</option>
                                    <option value="equity" {{ $account->type == 'equity' ? 'selected' : '' }}>رأس مال</option>
                                </select>
                            </div>
                        </div>

                        <div id="account_fields">
                            <div class="form-group">
                                <label>اختر الفئة الرئيسية</label>
                                <select name="parent_id" class="form-control">
                                    <option value="">-- اختر فئة --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $account->parent_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>طبيعة الحساب</label>
                                <select name="nature" class="form-control">
                                    <option value="">-- اختر --</option>
                                    <option value="debit" {{ $account->nature == 'debit' ? 'selected' : '' }}>مدين</option>
                                    <option value="credit" {{ $account->nature == 'credit' ? 'selected' : '' }}>دائن</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
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
