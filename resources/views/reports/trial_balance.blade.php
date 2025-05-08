@extends('layouts.app')
@section('title', __('messages.trial_balance'))
@section('content')
<div class="container">
    <h2 class="mb-4">@lang('messages.trial_balance')</h2>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="from" class="form-label">@lang('messages.from_date')</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-4">
            <label for="to" class="form-label">@lang('messages.to_date')</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-4">
            <label for="balance_type" class="form-label">@lang('messages.filter_by_balance')</label>
            <select name="balance_type" id="balance_type" class="form-select">
                <option value="">@lang('messages.all')</option>
                <option value="positive" {{ request('balance_type') == 'positive' ? 'selected' : '' }}>@lang('messages.positive_balance_only')</option>
                <option value="negative" {{ request('balance_type') == 'negative' ? 'selected' : '' }}>@lang('messages.negative_balance_only')</option>
            </select>
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">@lang('messages.show_report')</button>
        </div>
    </form>
    @if(isset($rows) && count($rows))
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('messages.account_code')</th>
                            <th>@lang('messages.account_name')</th>
                            <th>@lang('messages.debit')</th>
                            <th>@lang('messages.credit')</th>
                            <th>@lang('messages.balance')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['account']->code }}</td>
                            <td>{{ $row['account']->name }}</td>
                            <td>{{ number_format($row['debit'], 2) }}</td>
                            <td>{{ number_format($row['credit'], 2) }}</td>
                            <td>{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">@lang('messages.total')</th>
                            <th>{{ number_format($totalDebit, 2) }}</th>
                            <th>{{ number_format($totalCredit, 2) }}</th>
                            <th>{{ number_format($totalBalance, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 