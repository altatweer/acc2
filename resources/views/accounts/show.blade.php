@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-wallet text-primary"></i>
                    @lang('messages.account_details', ['name' => $account->name])
                </h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.real') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> @lang('messages.return')
                </a>
                @if(!$account->is_group)
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-edit"></i> @lang('messages.edit')
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

@php
    $typeLabels = [
        'receipt' => __('messages.voucher_receipt'),
        'payment' => __('messages.voucher_payment'),
        'transfer' => __('messages.voucher_transfer'),
        'deposit' => __('messages.voucher_deposit'),
        'withdraw' => __('messages.voucher_withdraw'),
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
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            @lang('messages.account_information')
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- معلومات الحساب الأساسية -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-alt"></i>
                                    المعلومات الأساسية
                                </h5>
                                <dl class="row">
                                    <dt class="col-sm-5">@lang('messages.account_code')</dt>
                                    <dd class="col-sm-7"><span class="badge badge-secondary">{{ $account->code }}</span></dd>

                                    <dt class="col-sm-5">@lang('messages.account_type')</dt>
                                    <dd class="col-sm-7">
                                        @php
                                            $typeColors = [
                                                'asset' => 'success',
                                                'liability' => 'danger', 
                                                'equity' => 'info',
                                                'revenue' => 'warning',
                                                'expense' => 'dark'
                                            ];
                                            $typeColor = $typeColors[$account->type] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $typeColor }}">{{ ucfirst($account->type) }}</span>
                                    </dd>

                                    <dt class="col-sm-5">@lang('messages.account_nature')</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge badge-{{ $account->nature == 'debit' ? 'primary' : 'success' }}">
                                            {{ $account->nature ?? __('messages.not_specified') }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-5">العملة الافتراضية</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge badge-info">{{ $account->default_currency ?? 'IQD' }}</span>
                                    </dd>

                                    @if($account->is_cash_box)
                                    <dt class="col-sm-5">نوع الحساب</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-cash-register"></i>
                                            صندوق نقدي
                                        </span>
                                    </dd>
                                    @endif
                                </dl>
                            </div>

                            <!-- أرصدة العملات -->
                            <div class="col-md-6">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-coins"></i>
                                    الأرصدة بالعملات
                                </h5>
                                <div class="row">
                                    @foreach($allCurrencies as $currency)
                                        @php
                                            $currencyBalance = $account->balance($currency);
                                        @endphp
                                        {{-- عرض جميع العملات النشطة، سواء كان لها رصيد أم لا --}}
                                        <div class="col-6 mb-3">
                                            <div class="small-box bg-{{ $currencyBalance >= 0 ? 'success' : 'danger' }}">
                                                <div class="inner">
                                                    <h4>{{ number_format(abs($currencyBalance), 2) }}</h4>
                                                    <p>{{ $currency }}</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-{{ $currencyBalance >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                                </div>
                                                {{-- إضافة تمييز بصري للعملة الافتراضية --}}
                                                @if($currency == $account->default_currency)
                                                <div class="small-box-footer bg-primary text-white">
                                                    <i class="fas fa-star"></i> عملة افتراضية
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Currency Filter Card -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i>
                            فلترة الحركات حسب العملة
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('accounts.show', $account) }}" class="row align-items-end">
                            <div class="col-md-3">
                                <label for="currency">العملة</label>
                                <select name="currency" id="currency" class="form-control">
                                    <option value="">جميع العملات</option>
                                    @foreach($allCurrencies as $currency)
                                        <option value="{{ $currency }}" {{ $selectedCurrency == $currency ? 'selected' : '' }}>
                                            {{ $currency }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                    تطبيق
                                </button>
                            </div>
                            <div class="col-md-7 text-right">
                                <span class="badge badge-info">
                                    الرصيد المعروض: {{ number_format($balance, 2) }} {{ $selectedCurrency ?: 'مختلط' }}
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transactions Table Card -->
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exchange-alt"></i>
                            @lang('messages.transactions')
                            @if($selectedCurrency)
                                <span class="badge badge-warning">{{ $selectedCurrency }}</span>
                            @endif
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info">{{ count($lines) }} حركة</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="accountMovementsTable" class="table table-hover text-center mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('messages.entry_number')</th>
                                        <th>@lang('messages.description')</th>
                                        <th>العملة</th>
                                        <th>@lang('messages.debit')</th>
                                        <th>@lang('messages.credit')</th>
                                        <th>@lang('messages.running_balance')</th>
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
                                            <td>
                                                <span class="text-muted">
                                                    {{ \Carbon\Carbon::parse($line->journalEntry->date ?? now())->format('Y-m-d') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($line->journalEntry)
                                                    <a href="#" class="journal-link badge badge-primary" data-journal-id="{{ $line->journalEntry->id }}">
                                                        #{{ $line->journalEntry->id }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ $line->description ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $line->currency ?? 'IQD' }}</span>
                                            </td>
                                            <td class="text-success">
                                                @if($line->debit > 0)
                                                    <strong>{{ number_format($line->debit, 2) }}</strong>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-danger">
                                                @if($line->credit > 0)
                                                    <strong>{{ number_format($line->credit, 2) }}</strong>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $runningBalance >= 0 ? 'success' : 'danger' }}">
                                                    {{ number_format($runningBalance, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-light">
                                    <tr>
                                        <th colspan="5">الإجمالي</th>
                                        <th class="text-success">{{ number_format($lines->sum('debit'), 2) }}</th>
                                        <th class="text-danger">{{ number_format($lines->sum('credit'), 2) }}</th>
                                        <th>
                                            <span class="badge badge-{{ $balance >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($balance, 2) }}
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Journal details modal --}}
<div class="modal fade" id="journalModal" tabindex="-1" role="dialog" aria-labelledby="journalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="journalModalLabel">
            <i class="fas fa-file-invoice"></i>
            @lang('messages.journal_details')
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="@lang('messages.close')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="journalModalBody">
        <div class="text-center text-muted">
            <i class="fas fa-spinner fa-spin"></i>
            @lang('messages.loading')
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<!-- DataTables CSS & JS from CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    $('#accountMovementsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/' + (document.documentElement.lang === 'ar' ? 'ar.json' : 'en.json')
        },
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        responsive: true,
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
        }
    });
    
    $('.journal-link').on('click', function(e){
        e.preventDefault();
        var journalId = $(this).data('journal-id');
        $('#journalModalBody').html('<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> @lang('messages.loading')</div>');
        $('#journalModal').modal('show');
        $.get('/journal-entries/' + journalId + '/modal?lang=' + '{{ app()->getLocale() }}', function(data){
            $('#journalModalBody').html(data);
        }).fail(function(){
            $('#journalModalBody').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> @lang('messages.error_fetching_journal')</div>');
        });
    });
});
</script>
@endpush
@endsection