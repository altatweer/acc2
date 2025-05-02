@extends('layouts.app')

@section('content')
<div class="content-wrapper">
   <div class="content-header">
       <div class="container-fluid">
           <h1 class="m-0">الحركات المالية (من القيود المحاسبية)</h1>
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
                               <th>تاريخ القيد</th>
                               <th>رقم القيد</th>
                               <th>الحساب</th>
                               <th>مدين</th>
                               <th>دائن</th>
                               <th>العملة</th>
                               <th>الوصف</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($lines as $i => $line)
                               <tr>
                                   <td>{{ $lines->firstItem() + $i }}</td>
                                   <td>{{ $line->journalEntry->date ?? '-' }}</td>
                                   <td>{{ $line->journalEntry->id ?? '-' }}</td>
                                   <td>{{ $line->account->name ?? '-' }}</td>
                                   <td>{{ number_format($line->debit, 2) }}</td>
                                   <td>{{ number_format($line->credit, 2) }}</td>
                                   <td>{{ $line->currency }}</td>
                                   <td>{{ $line->description }}</td>
                               </tr>
                           @endforeach
                       </tbody>
                   </table>

                   {{ $lines->links() }}

               </div>
           </div>
       </div>
   </section>
</div>
@endsection