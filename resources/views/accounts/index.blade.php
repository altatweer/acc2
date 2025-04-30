@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة الحسابات الفعلية</h1>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary mt-3">إضافة حساب / فئة</a>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الحساب</th>
                                <th>اسم الفئة الرئيسية</th>
                                <th>نوع الحساب المحاسبي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->parent ? $account->parent->name : '-' }}</td>
                                    <td>
                                        @switch($account->parent->type ?? null)
                                            @case('asset') أصول @break
                                            @case('liability') التزامات @break
                                            @case('revenue') إيرادات @break
                                            @case('expense') مصاريف @break
                                            @case('equity') رأس مال @break
                                            @default -
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-info btn-sm">تعديل</a>
                                        <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')" class="btn btn-danger btn-sm">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
