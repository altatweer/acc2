@extends('layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">إدارة الفواتير</h1>
        </div>
        <div class="col-sm-6 text-left">
          <a href="{{ route('invoices.create') }}" class="btn btn-primary">إنشاء فاتورة جديدة</a>
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
                  <span class="badge badge-{{ $inv->status=='paid'?'success':($inv->status=='partial'?'warning':'secondary') }}">
                    {{ ['unpaid'=>'غير مدفوعة','partial'=>'مدفوعة جزئي','paid'=>'مدفوعة'][$inv->status] }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-info">عرض</a>
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
</div>
@endsection 