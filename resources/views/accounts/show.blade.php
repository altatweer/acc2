@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>تفاصيل الحساب: {{ $account->name }}</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.real') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>
</section>

@php
    $typeLabels = [
        'receipt' => 'سند قبض',
        'payment' => 'سند صرف',
        'transfer' => 'سند تحويل',
        'deposit' => 'إيداع نقدي',
        'withdraw' => 'سحب نقدي',
    ];
@endphp

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Account Info Card -->
                <div class="card card-info card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">معلومات الحساب</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-4">
                            <dt class="col-sm-3">كود الحساب</dt>
                            <dd class="col-sm-9">{{ $account->code }}</dd>

                            <dt class="col-sm-3">نوع الحساب</dt>
                            <dd class="col-sm-9">{{ ucfirst($account->type) }}</dd>

                            <dt class="col-sm-3">طبيعة الحساب</dt>
                            <dd class="col-sm-9">{{ $account->nature ?? 'غير محددة' }}</dd>

                            <dt class="col-sm-3">الرصيد ({{ $account->currency }})</dt>
                            <dd class="col-sm-9 font-weight-bold">{{ $balance >= 0 ? '+' : '-' }}{{ number_format(abs($balance), 2) }}</dd>
                        </dl>
                    </div>
                </div>
                <!-- Transactions Table Card -->
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">الحركات</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>التاريخ</th>
                                        <th>رقم القيد</th>
                                        <th>الوصف</th>
                                        <th>مدين</th>
                                        <th>دائن</th>
                                        <th>الرصيد التراكمي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $runningBalance = 0; @endphp
                                    @foreach($lines as $line)
                                        @php
                                            $runningBalance += $line->debit - $line->credit;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $line->journalEntry->date ?? '-' }}</td>
                                            <td>
                                                @if($line->journalEntry)
                                                    <a href="#" class="journal-link" data-journal-id="{{ $line->journalEntry->id }}">{{ $line->journalEntry->id }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $line->description ?? '-' }}</td>
                                            <td>{{ number_format($line->debit, 2) }}</td>
                                            <td>{{ number_format($line->credit, 2) }}</td>
                                            <td>{{ number_format($runningBalance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- نافذة منبثقة لعرض تفاصيل القيد --}}
<div class="modal fade" id="journalModal" tabindex="-1" role="dialog" aria-labelledby="journalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="journalModalLabel">تفاصيل القيد المحاسبي</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="journalModalBody">
        <div class="text-center text-muted">جاري التحميل...</div>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
$(function(){
    $('.journal-link').on('click', function(e){
        e.preventDefault();
        var journalId = $(this).data('journal-id');
        $('#journalModalBody').html('<div class="text-center text-muted">جاري التحميل...</div>');
        $('#journalModal').modal('show');
        $.get('/journal-entries/' + journalId + '/modal', function(data){
            $('#journalModalBody').html(data);
        }).fail(function(){
            $('#journalModalBody').html('<div class="alert alert-danger">حدث خطأ أثناء جلب تفاصيل القيد.</div>');
        });
    });
});
</script>
@endpush
@endsection 