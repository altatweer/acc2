@extends('layouts.app')

@section('content')
<div class="content-header">
   <div class="container-fluid">
       <h1 class="m-0">الحركات المالية (من القيود المحاسبية)</h1>
   </div>
</div>
<section class="content">
   <div class="container-fluid">
       <div class="card mt-3">
           <div class="card-header">
               <div class="card-tools">
                   @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                   <!-- تم إخفاء زر إضافة حركة مالية بناءً على طلب الإدارة -->
                   <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                   <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
               </div>
           </div>
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
                           <th>الإجراءات</th>
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
                               <td>
                                   <!-- تم إخفاء جميع الأزرار بناءً على طلب الإدارة -->
                               </td>
                           </tr>
                       @endforeach
                   </tbody>
               </table>

               {{ $lines->links() }}

           </div>
       </div>
   </div>
</section>
@endsection