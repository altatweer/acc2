<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.ledger_report') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>{{ __('messages.ledger_report') }}</h2>
    <p><strong>{{ __('messages.account') }}:</strong> {{ $accounts->find($selectedAccount)->name }}</p>
    <p><strong>{{ __('messages.from_date') }} - {{ __('messages.to_date') }}:</strong> {{ $from }} - {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.entry_number') }}</th>
                <th>{{ __('messages.date') }}</th>
                <th>{{ __('messages.description') }}</th>
                <th>{{ __('messages.debit') }}</th>
                <th>{{ __('messages.credit') }}</th>
                <th>{{ __('messages.balance_after_transaction') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5">{{ __('messages.opening_balance') }}</td>
                <td>{{ number_format($openingBalance, 2) }}</td>
            </tr>
            @php $balance = $openingBalance; @endphp
            @foreach($entries as $entry)
                @php $balance += $entry->debit - $entry->credit; @endphp
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
        <tfoot>
            <tr>
                <th colspan="3">{{ __('messages.total') }}</th>
                <th>{{ number_format($totalDebit, 2) }}</th>
                <th>{{ number_format($totalCredit, 2) }}</th>
                <th>{{ number_format($balance, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html> 