@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">عرض تفاصيل السند</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="card mt-3">
                <div class="card-body">

                    <h5>معلومات السند:</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>رقم السند</th>
                            <td>{{ $voucher->voucher_number }}</td>
                        </tr>
                        <tr>
                            <th>نوع السند</th>
                            <td>{{ $voucher->type == 'receipt' ? 'قبض' : ($voucher->type == 'payment' ? 'صرف' : 'تحويل') }}</td>
                        </tr>
                        <tr>
                            <th>التاريخ</th>
                            <td>{{ $voucher->date ? \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>المحاسب</th>
                            <td>{{ $voucher->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>المستلم/الدافع</th>
                            <td>{{ $voucher->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td>{{ $voucher->description }}</td>
                        </tr>
                    </table>

                    <hr>

                    <h5>الحركات المالية المرتبطة:</h5>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>الحساب الرئيسي</th>
                                <th>الحساب المستهدف</th>
                                <th>المبلغ</th>
                                <th>العملة</th>
                                <th>سعر الصرف</th>
                                <th>الوصف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($voucher->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->account->name ?? '-' }}</td>
                                    <td>{{ $transaction->targetAccount->name ?? '-' }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->currency }}</td>
                                    <td>{{ $transaction->exchange_rate }}</td>
                                    <td>{{ $transaction->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('vouchers.print', $voucher->id) }}" class="btn btn-success" target="_blank">طباعة السند</a>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
