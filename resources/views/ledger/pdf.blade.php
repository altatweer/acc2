<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>دفتر الأستاذ - {{ $account->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>دفتر الأستاذ</h2>
        <h3>{{ $account->name }} ({{ $account->code }})</h3>
        @if($from && $to)
            <p>من {{ $from }} إلى {{ $to }}</p>
        @elseif($from)
            <p>من {{ $from }}</p>
        @elseif($to)
            <p>حتى {{ $to }}</p>
        @endif
    </div>
    
    <table>
        <thead>
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
                <th style="text-align: right;">{{ number_format($finalBalance ?? $balance, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
