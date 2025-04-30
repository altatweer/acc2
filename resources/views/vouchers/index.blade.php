@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">قائمة السندات</h1>
            <a href="{{ route('vouchers.create') }}" class="btn btn-primary mt-3">إنشاء سند جديد</a>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <form method="GET" action="{{ route('vouchers.index') }}" class="form-inline mb-3">
                <div class="form-group mx-sm-2">
                    <select name="type" class="form-control">
                        <option value="">-- نوع السند --</option>
                        <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>قبض</option>
                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>صرف</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>تحويل</option>
                    </select>
                </div>

                <div class="form-group mx-sm-2">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control" placeholder="تاريخ السند">
                </div>

                <div class="form-group mx-sm-2">
                    <input type="text" name="recipient_name" value="{{ request('recipient_name') }}" class="form-control" placeholder="اسم المستلم / الدافع">
                </div>

                <button type="submit" class="btn btn-primary mx-sm-2">بحث</button>
                <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">مسح البحث</a>
            </form>

            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>رقم السند</th>
                                <th>نوع السند</th>
                                <th>التاريخ</th>
                                <th>المحاسب</th>
                                <th>المستلم/الدافع</th>
                                <th>التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td>{{ $voucher->voucher_number }}</td>
                                    <td>{{ $voucher->type }}</td>
                                    <td>{{ $voucher->date }}</td>
                                    <td>{{ $voucher->user->name ?? '-' }}</td>
                                    <td>{{ $voucher->recipient_name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('vouchers.show', $voucher->id) }}" class="btn btn-sm btn-info">عرض</a>
                                        <a href="{{ route('vouchers.print', $voucher->id) }}" class="btn btn-sm btn-success" target="_blank">طباعة</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $vouchers->links() }}

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
