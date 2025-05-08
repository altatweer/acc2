@extends('layouts.print')

@section('print-content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h4 class="mb-4 text-center text-primary">@lang('messages.journal_entry') #{{ $journalEntry->id }}</h4>
                <table class="table table-bordered mb-4">
                    <tr>
                        <th>@lang('messages.date')</th>
                        <td>{{ $journalEntry->date }}</td>
                        <th>@lang('messages.description')</th>
                        <td>{{ $journalEntry->description }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.user')</th>
                        <td>{{ $journalEntry->user->name ?? '-' }}</td>
                        <th>@lang('messages.currency')</th>
                        <td>{{ $journalEntry->currency }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.debit')</th>
                        <td>{{ number_format($journalEntry->total_debit,2) }}</td>
                        <th>@lang('messages.credit')</th>
                        <td>{{ number_format($journalEntry->total_credit,2) }}</td>
                    </tr>
                    <tr>
                        <th>@lang('messages.status')</th>
                        <td colspan="3">
                            @if($journalEntry->status == 'active')
                                <span class="badge badge-success">@lang('messages.status_active')</span>
                            @else
                                <span class="badge badge-danger">@lang('messages.status_cancelled')</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <h5 class="mb-3">@lang('messages.lines'):</h5>
                <table class="table table-bordered table-striped text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>@lang('messages.account')</th>
                            <th>@lang('messages.description')</th>
                            <th>@lang('messages.debit')</th>
                            <th>@lang('messages.credit')</th>
                            <th>@lang('messages.currency')</th>
                            <th>@lang('messages.exchange_rate')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntry->lines as $i=>$line)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $line->account->name ?? '-' }}</td>
                            <td>{{ $line->description }}</td>
                            <td>{{ number_format($line->debit,2) }}</td>
                            <td>{{ number_format($line->credit,2) }}</td>
                            <td>{{ $line->currency }}</td>
                            <td>{{ $line->exchange_rate }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-5 row">
                    <div class="col text-center">
                        <span class="d-inline-block border-top pt-2 px-4">@lang('messages.accountant_signature')</span>
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