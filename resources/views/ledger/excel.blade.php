<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 16px; font-weight: bold;">
                دفتر الأستاذ - {{ $account->name }}
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center;">
                @if($from && $to)
                    من {{ $from }} إلى {{ $to }}
                @elseif($from)
                    من {{ $from }}
                @elseif($to)
                    حتى {{ $to }}
                @endif
            </th>
        </tr>
        <tr>
            <th>#</th>
            <th>التاريخ</th>
            <th>الوصف</th>
            <th>مدين</th>
            <th>دائن</th>
            <th>الرصيد</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" style="text-align: right; font-weight: bold;">الرصيد الافتتاحي</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($openingBalance, 2) }}</td>
        </tr>
        @php 
            $balance = $openingBalance;
            $index = 1;
        @endphp
        @foreach($entries as $entry)
            @php
                $debit = is_object($entry) ? $entry->debit : $entry['debit'];
                $credit = is_object($entry) ? $entry->credit : $entry['credit'];
                $balance += ($account->nature === 'مدين' || $account->nature === 'debit') 
                    ? ($debit - $credit)
                    : ($credit - $debit);
            @endphp
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ is_object($entry) && $entry->journalEntry ? $entry->journalEntry->date : (isset($entry['date']) ? $entry['date'] : '-') }}</td>
                <td>{{ is_object($entry) ? $entry->description : ($entry['description'] ?? '-') }}</td>
                <td style="text-align: right;">{{ number_format($debit, 2) }}</td>
                <td style="text-align: right;">{{ number_format($credit, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($balance, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">الإجمالي</th>
            <th style="text-align: right;">{{ number_format($totalDebit, 2) }}</th>
            <th style="text-align: right;">{{ number_format($totalCredit, 2) }}</th>
            <th style="text-align: right;">{{ number_format($finalBalance, 2) }}</th>
        </tr>
    </tfoot>
</table>
