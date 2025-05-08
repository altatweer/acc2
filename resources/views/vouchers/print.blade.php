@extends('layouts.print')

@section('print-content')
<div class="alert alert-warning">
    @lang('messages.transactions_count') {{ $transactions->count() }}
</div>
<div class="no-print text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary px-4">@lang('messages.print')</button>
</div>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="mb-4 text-center text-primary">@lang('messages.financial_voucher_number', ['number' => $voucher->voucher_number])</h4>
                <table class="table table-bordered mb-4">
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
                        <th>@lang('messages.voucher_date')</th>
                        <td>{{ $voucher->date ? \Illuminate\Support\Carbon::parse($voucher->date)->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.accountant')</th>
                        <td>{{ $voucher->user->name ?? '-' }}</td>
                        <th>@lang('messages.recipient_payer')</th>
                        <td>{{ $voucher->recipient_name }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.description')</th>
                        <td colspan="3">{{ $voucher->description }}</td>
                    </tr>
                </table>
                <h5 class="mb-3">@lang('messages.related_financial_transactions')</h5>
                <table class="table table-bordered table-striped text-center">
                    <thead class="thead-light">
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
                            <tr>
                                <td colspan="5" class="text-center">@lang('messages.no_financial_transactions')</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-5 row">
                    <div class="col text-center">
                        <span class="d-inline-block border-top pt-2 px-4">@lang('messages.accountant_signature')</span>
                    </div>
                    <div class="col text-center">
                        <span class="d-inline-block border-top pt-2 px-4">@lang('messages.recipient_signature')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection
