<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المصروفات والإيرادات</title>
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('storage/fonts/Amiri-Regular.ttf') format('truetype');
        }
        body { font-family: 'Amiri', 'DejaVu Sans', sans-serif !important; direction: rtl; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: right; }
        th { background: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>تقرير المصروفات والإيرادات</h2>
    <div style="margin-bottom: 10px; text-align: center;">
        @if($from && $to)
            <span>الفترة: من {{ $from }} إلى {{ $to }}</span>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>البند</th>
                <th>إيرادات</th>
                <th>مصروفات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row['account']->name }}</td>
                <td>
                    @if(in_array($row['type'], ['إيراد', 'revenue']))
                        {{ number_format(abs($row['balance']), 2) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if(in_array($row['type'], ['مصروف', 'expense']))
                        {{ number_format(abs($row['balance']), 2) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center">لا توجد بيانات</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>الإجمالي</th>
                <th>{{ number_format($totalRevenue, 2) }}</th>
                <th>{{ number_format($totalExpense, 2) }}</th>
            </tr>
            <tr>
                @php $net = abs($totalRevenue) - abs($totalExpense); @endphp
                @if($net >= 0)
                    <th colspan="2">صافي الربح</th>
                @else
                    <th colspan="2">صافي الخسارة</th>
                @endif
                <th>{{ number_format(abs($net), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html> 