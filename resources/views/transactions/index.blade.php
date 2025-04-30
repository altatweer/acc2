@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <div class="content-header">
       <div class="container-fluid">
           <h1 class="m-0">الحركات المالية</h1>
       </div>
   </div>

   <section class="content">
       <div class="container-fluid">
           <div class="card mt-3">
               <div class="card-body">
                   <table class="table table-bordered table-striped">
                       <thead>
                           <tr>
                               <th>تاريخ الحركة</th>
                               <th>رقم السند</th>
                               <th>نوع الحركة</th>
                               <th>الحساب المصدر</th>
                               <th>الحساب المستهدف</th>
                               <th>المبلغ</th>
                               <th>العملة</th>
                               <th>سعر الصرف</th>
                               <th>الوصف</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($transactions as $transaction)
                               <tr>
                                   <td>{{ $transaction->date }}</td>
                                   <td>{{ $transaction->voucher->voucher_number ?? '-' }}</td>
                                   <td>{{ $transaction->type }}</td>
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

                   {{ $transactions->links() }}

               </div>
           </div>
       </div>
   </section>
</div>
@endsection