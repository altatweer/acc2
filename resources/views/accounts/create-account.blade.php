@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>إضافة حساب فعلي</h3>
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

            <form action="{{ route('accounts.storeAccount') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>اسم الحساب</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>رمز الحساب</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>اختر الفئة الرئيسية</label>
                    <select name="parent_id" class="form-control" required>
                        <option value="">-- اختر --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>طبيعة الحساب</label>
                    <select name="nature" class="form-control" required>
                        <option value="">-- اختر --</option>
                        <option value="debit">مدين</option>
                        <option value="credit">دائن</option>
                    </select>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_cash_box" class="form-check-input" id="cashBoxCheck">
                    <label class="form-check-label" for="cashBoxCheck">هل هو صندوق نقدي؟</label>
                </div>

                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('accounts.real') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </section>
</div>
@endsection
