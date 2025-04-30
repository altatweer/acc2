@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>إضافة فئة رئيسية أو فرعية</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if ($errors->any())
                <div class="alert alert-danger">
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
                    <label>اسم الفئة</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>رمز الفئة</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>نوع الحساب</label>
                    <select name="type" class="form-control" required>
                        <option value="">-- اختر --</option>
                        <option value="asset">أصول</option>
                        <option value="liability">خصوم</option>
                        <option value="revenue">إيرادات</option>
                        <option value="expense">مصروفات</option>
                        <option value="equity">حقوق ملكية</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>الفئة الرئيسية (اختياري)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- لا شيء --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </section>
</div>
@endsection
