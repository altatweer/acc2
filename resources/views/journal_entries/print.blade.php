@extends('layouts.print')

@section('print-content')
<div class="no-print print-actions text-center mb-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> @lang('messages.print')
    </button>
</div>

<div class="document-title">
    <h3>@lang('messages.journal_entry') #{{ $journalEntry->id }}</h3>
</div>

<div class="document-info p-3 mb-4">
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.date'):</strong>
            <span>{{ $journalEntry->date }}</span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.status'):</strong>
            <span class="badge badge-{{ $journalEntry->status == 'active' ? 'success' : 'danger' }} ml-2">
                @if($journalEntry->status == 'active')
                    @lang('messages.status_active')
                @else
                    @lang('messages.status_cancelled')
                @endif
            </span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.user'):</strong>
            <span>{{ $journalEntry->user->name ?? '-' }}</span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.currency'):</strong>
            <span>{{ $journalEntry->currency }}</span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <strong>@lang('messages.total_debit'):</strong>
            <span class="font-weight-bold">{{ number_format($journalEntry->total_debit, 2) }}</span>
        </div>
        <div class="col-6">
            <strong>@lang('messages.total_credit'):</strong>
            <span class="font-weight-bold">{{ number_format($journalEntry->total_credit, 2) }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <strong>@lang('messages.description'):</strong>
            <span>{{ $journalEntry->description }}</span>
        </div>
    </div>
</div>

<div class="lines-section mb-4">
    <h4 class="section-title">@lang('messages.journal_entry_lines')</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-header">
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
                    <td class="text-right">{{ number_format($line->debit, 2) }}</td>
                    <td class="text-right">{{ number_format($line->credit, 2) }}</td>
                    <td>{{ $line->currency }}</td>
                    <td class="text-right">{{ number_format($line->exchange_rate, 4) }}</td>
                </tr>
                @endforeach
                <tr class="table-total">
                    <td colspan="3"><strong>@lang('messages.total')</strong></td>
                    <td class="text-right"><strong>{{ number_format($journalEntry->total_debit, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($journalEntry->total_credit, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="signature-section">
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">@lang('messages.accountant_signature')</div>
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <div class="signature-title">@lang('messages.finance_manager_signature')</div>
    </div>
</div>

<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 500);
    };
</script>
@endsection 