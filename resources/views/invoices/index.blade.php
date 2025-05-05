@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">إدارة الفواتير</h1>
      </div>
      <div class="col-sm-6 text-left">
        @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
        @if($isSuperAdmin || auth()->user()->can('إضافة فاتورة'))
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">إنشاء فاتورة جديدة</a>
        @endif
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">قائمة الفواتير</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>رقم الفاتورة</th>
              <th>العميل</th>
              <th>التاريخ</th>
              <th>الإجمالي</th>
              <th>العملة</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoices as $inv)
            <tr>
              <td>{{ $invoices->firstItem() + $loop->index }}</td>
              <td>{{ $inv->invoice_number }}</td>
              <td>{{ $inv->customer->name }}</td>
              <td>{{ $inv->date->format('Y-m-d') }}</td>
              <td>{{ number_format($inv->total,2) }}</td>
              <td>{{ $inv->currency }}</td>
              <td>
                @php
                  $statusLabels = ['draft'=>'مسودة','unpaid'=>'غير مدفوعة','partial'=>'مدفوعة جزئي','paid'=>'مدفوعة'];
                  $badgeClass = $inv->status=='draft' ? 'secondary' : ($inv->status=='unpaid' ? 'warning' : ($inv->status=='partial' ? 'info' : 'success'));
                @endphp
                <span class="badge badge-{{ $badgeClass }}">
                  {{ $statusLabels[$inv->status] ?? $inv->status }}
                </span>
              </td>
              <td>
                @if($isSuperAdmin || auth()->user()->can('عرض الفواتير'))
                <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-info">عرض</a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-between align-items-center">
        <div>إجمالي الفواتير: <strong>{{ $invoices->total() }}</strong></div>
        <div>{{ $invoices->links() }}</div>
      </div>
    </div>
  </div>
</section>
@endsection 