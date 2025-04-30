@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>إدارة الفئات الرئيسية</h3>
            <a href="{{ route('accounts.createGroup') }}" class="btn btn-primary">إضافة فئة جديدة</a>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>رقم الفئة</th>
                                <th>اسم الفئة</th>
                                <th>نوع الحساب</th>
                                <th>الفئة الرئيسية</th>
                                <th>الخيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $account->code }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>
                                        @php
                                            $types = [
                                                'asset' => 'أصول',
                                                'liability' => 'خصوم',
                                                'revenue' => 'إيرادات',
                                                'expense' => 'مصروفات',
                                                'equity' => 'حقوق ملكية'
                                            ];
                                        @endphp
                                        {{ $types[$account->type] ?? '-' }}
                                    </td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-info">تعديل</a>
                                        <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
