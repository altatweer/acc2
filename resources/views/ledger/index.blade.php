@extends('layouts.app')

@section('title', __('sidebar.ledger'))

@section('content')
<div class="container">
    <h2 class="mb-4">@lang('messages.ledger_report')</h2>
    <form method="GET" action="{{ route('ledger.index') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="account_id" class="form-label">@lang('messages.account')</label>
            <select name="account_id" id="account_id" class="form-select" required>
                <option value="">@lang('messages.select_account')</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ $selectedAccount == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">@lang('messages.show_report')</button>
        </div>
    </form>

    @if($selectedAccount)
        <div class="mb-3">
            <a href="{{ Route::localizedRoute('ledger.index', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> @lang('messages.export_pdf')
            </a>
            <a href="{{ Route::localizedRoute('ledger.index', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> @lang('messages.export_excel')
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> @lang('messages.print')
            </button>
        </div>
        <div class="card">
            <div class="card-header bg-light">
                <strong>@lang('messages.account'):</strong> {{ $accounts->find($selectedAccount)->name }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('messages.entry_number')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('messages.description')</th>
                                <th>@lang('messages.debit')</th>
                                <th>@lang('messages.credit')</th>
                                <th>@lang('messages.balance_after_transaction')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-end"><strong>@lang('messages.opening_balance')</strong></td>
                                <td><strong>{{ number_format($openingBalance, 2) }}</strong></td>
                            </tr>
                            @php $balance = $openingBalance; @endphp
                            @foreach($entries as $entry)
                                @php
                                    $balance += $entry->debit - $entry->credit;
                                @endphp
                                <tr>
                                    <td>{{ $entry->journalEntry->id ?? '-' }}</td>
                                    <td>{{ $entry->journalEntry->date ?? '-' }}</td>
                                    <td>{{ $entry->description }}</td>
                                    <td>{{ number_format($entry->debit, 2) }}</td>
                                    <td>{{ number_format($entry->credit, 2) }}</td>
                                    <td>{{ number_format($balance, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">@lang('messages.total')</th>
                                <th>{{ number_format($totalDebit, 2) }}</th>
                                <th>{{ number_format($totalCredit, 2) }}</th>
                                <th>{{ number_format($balance, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 