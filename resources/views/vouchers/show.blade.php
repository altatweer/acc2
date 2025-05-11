@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="alert alert-warning">@lang('messages.debug_voucher_status') <b>{{ $voucher->status }}</b></div>
            <div class="alert alert-info">@lang('messages.debug_journal_entry') {{ $voucher->journalEntry ? __('messages.debug_yes') : __('messages.debug_no') }} | @lang('messages.debug_line_count') {{ $voucher->journalEntry && $voucher->journalEntry->lines ? $voucher->journalEntry->lines->count() : 0 }}</div>
            <h1 class="m-0">@lang('messages.voucher_details_title')</h1>
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
                    @lang('messages.voucher_canceled_alert')
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-body">

                    <h5>@lang('messages.voucher_information')</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('messages.voucher_number')</th>
                            <td>{{ $voucher->voucher_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.voucher_type')</th>
                            <td>
                                @if($voucher->type == 'receipt')
                                    @lang('messages.receipt')
                                @elseif($voucher->type == 'payment')
                                    @lang('messages.payment')
                                @else
                                    @lang('messages.transfer')
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>@lang('messages.voucher_date')</th>
                            <td>{{ $voucher->date ? $voucher->date->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.accountant')</th>
                            <td>{{ $voucher->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.recipient_payer')</th>
                            <td>{{ $voucher->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('messages.description')</th>
                            <td>{{ $voucher->description }}</td>
                        </tr>
                    </table>

                    <hr>

                    <h5>@lang('messages.related_financial_transactions')</h5>

                    @php
                        $voucherStatus = $voucher->status;
                        if (is_null($voucherStatus)) $voucherStatus = 'active';
                    @endphp
                    @if(trim((string)$voucherStatus) === 'active')
                        <a href="{{ Route::localizedRoute('vouchers.print', ['voucher' => $voucher->id, ]) }}" class="btn btn-success" target="_blank">@lang('messages.print_voucher')</a>
                        @can('cancel_vouchers')
                        <form action="{{ Route::localizedRoute('vouchers.cancel', ['voucher' => $voucher, ]) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.cancel_voucher_confirm')')">@lang('messages.cancel_voucher')</button>
                        </form>
                        @endcan
                    @else
                        <div class="mt-3 alert alert-info">
                            <strong>@lang('messages.note'):</strong> @lang('messages.voucher_edit_note')
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>@lang('messages.account')</th>
                                <th>@lang('messages.debit')</th>
                                <th>@lang('messages.credit')</th>
                                <th>@lang('messages.currency')</th>
                                <th>@lang('messages.description')</th>
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
                                <tr><td colspan="5">@lang('messages.no_transactions')</td></tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
