@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>تعديل فئة</h3>
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

            <form action="{{ route('accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="is_group" value="1">

                <div class="form-group">
                    <label>اسم الفئة</label>
                    <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                </div>

                <div class="form-group">
                    <label>رمز الفئة</label>
                    <input type="text" class="form-control" value="{{ $account->code }}" disabled>
                    <input type="hidden" name="code" value="{{ $account->code }}">
                </div>

                <div class="form-group">
                    <label>نوع الحساب</label>
                    <select name="type" class="form-control" required>
                        <option value="">-- اختر --</option>
                        <option value="asset" {{ $account->type == 'asset' ? 'selected' : '' }}>أصول</option>
                        <option value="liability" {{ $account->type == 'liability' ? 'selected' : '' }}>خصوم</option>
                        <option value="revenue" {{ $account->type == 'revenue' ? 'selected' : '' }}>إيرادات</option>
                        <option value="expense" {{ $account->type == 'expense' ? 'selected' : '' }}>مصروفات</option>
                        <option value="equity" {{ $account->type == 'equity' ? 'selected' : '' }}>حقوق ملكية</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>الفئة الرئيسية (اختياري)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">-- لا شيء --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $account->parent_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success">تحديث</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </section>
</div>
@endsection
