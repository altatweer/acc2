@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="alert alert-warning">[تشخيص] حالة السند الحالية: <b>{{ $voucher->status }}</b></div>
            <div class="alert alert-info">[تشخيص] قيد محاسبي مرتبط: {{ $voucher->journalEntry ? 'نعم' : 'لا' }} | عدد السطور: {{ $voucher->journalEntry && $voucher->journalEntry->lines ? $voucher->journalEntry->lines->count() : 0 }}</div>
            <h1 class="m-0">عرض تفاصيل السند</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($voucher->status == 'canceled')
                <div class="alert alert-danger text-center font-weight-bold">
                    هذا السند ملغي (تم توليد قيد عكسي تلقائيًا لإبطال أثره المحاسبي).<br>
                    لا يمكن طباعته أو استخدامه في أي عملية مالية.
                </div>
            @endif

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
                            <td>{{ $voucher->date ? $voucher->date->format('Y-m-d H:i:s') : '-' }}</td>
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

                    @php
                        $voucherStatus = $voucher->status;
                        if (is_null($voucherStatus)) $voucherStatus = 'active';
                    @endphp
                    @if(trim((string)$voucherStatus) === 'active')
                        <a href="{{ route('vouchers.print', $voucher->id) }}" class="btn btn-success" target="_blank">طباعة السند</a>
                        <form action="{{ route('vouchers.cancel', $voucher) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من إلغاء السند؟ سيتم توليد قيد عكسي ولن يمكن التراجع.')">إلغاء السند</button>
                        </form>
                    @else
                        <div class="mt-3 alert alert-info">
                            <strong>ملاحظة:</strong> لا يمكن حذف أو تعديل السندات المالية بعد اعتمادها. في حال وجود خطأ، يتم إلغاء السند وتوليد قيد عكسي تلقائيًا وفقًا للمعايير المحاسبية الدولية (IFRS/GAAP)، ويجب إنشاء سند جديد بالقيم الصحيحة.
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>الحساب</th>
                                <th>مدين</th>
                                <th>دائن</th>
                                <th>العملة</th>
                                <th>الوصف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($voucher->journalEntry && $voucher->journalEntry->lines && $voucher->journalEntry->lines->count())
                                @foreach($voucher->journalEntry->lines as $line)
                                    <tr>
                                        <td>{{ $line->account->name ?? '-' }}</td>
                                        <td>{{ number_format($line->debit, 2) }}</td>
                                        <td>{{ number_format($line->credit, 2) }}</td>
                                        <td>{{ $line->currency }}</td>
                                        <td>{{ $line->description }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5">لا توجد حركات مرتبطة بهذا السند.</td></tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
