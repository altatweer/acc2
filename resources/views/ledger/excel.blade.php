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