@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>@lang('messages.account_details', ['name' => $account->name])</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.real') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> @lang('messages.return')
                </a>
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
                        <h3 class="card-title">@lang('messages.account_information')</h3>
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
                            <dt class="col-sm-3">@lang('messages.account_code')</dt>
                            <dd class="col-sm-9">{{ $account->code }}</dd>

                            <dt class="col-sm-3">@lang('messages.account_type')</dt>
                            <dd class="col-sm-9">{{ ucfirst($account->type) }}</dd>

                            <dt class="col-sm-3">@lang('messages.account_nature')</dt>
                            <dd class="col-sm-9">{{ $account->nature ?? __('messages.not_specified') }}</dd>

                            <dt class="col-sm-3">@lang('messages.balance', ['currency' => $account->currency])</dt>
                            <dd class="col-sm-9 font-weight-bold">{{ $balance >= 0 ? '+' : '-' }}{{ number_format(abs($balance), 2) }}</dd>
                        </dl>
                    </div>
                </div>
                <!-- Transactions Table Card -->
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">@lang('messages.transactions')</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="accountMovementsTable" class="table table-hover text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('messages.entry_number')</th>
                                        <th>@lang('messages.description')</th>
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

{{-- Journal details modal --}}
<div class="modal fade" id="journalModal" tabindex="-1" role="dialog" aria-labelledby="journalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="journalModalLabel">@lang('messages.journal_details')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="@lang('messages.close')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="journalModalBody">
        <div class="text-center text-muted">@lang('messages.loading')</div>
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
        responsive: true
    });
    $('.journal-link').on('click', function(e){
        e.preventDefault();
        var journalId = $(this).data('journal-id');
        $('#journalModalBody').html('<div class="text-center text-muted">@lang('messages.loading')</div>');
        $('#journalModal').modal('show');
        $.get('/journal-entries/' + journalId + '/modal?lang=' + '{{ app()->getLocale() }}', function(data){
            $('#journalModalBody').html(data);
        }).fail(function(){
            $('#journalModalBody').html('<div class="alert alert-danger">@lang('messages.error_fetching_journal')</div>');
        });
    });
});
</script>
@endpush
@endsection