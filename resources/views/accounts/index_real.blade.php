@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3>عرض الحسابات الفعلية</h3>
            <a href="{{ route('accounts.createAccount') }}" class="btn btn-primary">إضافة حساب فعلي</a>
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
                                <th>رقم الحساب</th>
                                <th>اسم الحساب</th>
                                <th>الفئة الرئيسية</th>
                                <th>طبيعة الحساب</th>
                                <th>صندوق نقدي؟</th>
                                <th>الخيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $account->code }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->parent->name ?? '-' }}</td>
                                    <td>
                                        @if ($account->nature == 'debit') مدين
                                        @elseif ($account->nature == 'credit') دائن
                                        @else -
                                        @endif
                                    </td>
                                    <td>{{ $account->is_cash_box ? 'نعم' : 'لا' }}</td>
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

                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
