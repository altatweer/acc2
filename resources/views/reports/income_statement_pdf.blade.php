<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قائمة الدخل</title>
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
    <h2>قائمة الدخل (Income Statement)</h2>
    <div style="margin-bottom: 10px; text-align: center;">
        @if($from && $to)
            <span>الفترة: من {{ $from }} إلى {{ $to }}</span>
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>الحساب</th>
                <th>مدين</th>
                <th>دائن</th>
                <th>الرصيد</th>
                <th>النوع</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['account']->name }}</td>
                <td>{{ str_replace(['–', '−', '—'], '-', number_format($row['debit'], 2)) }}</td>
                <td>{{ str_replace(['–', '−', '—'], '-', number_format($row['credit'], 2)) }}</td>
                <td>{{ str_replace(['–', '−', '—'], '-', number_format($row['balance'], 2)) }}</td>
                <td>{{ $row['type'] }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">إجمالي الإيرادات</th>
                <th colspan="2">{{ str_replace(['–', '−', '—'], '-', number_format($totalRevenue, 2)) }}</th>
            </tr>
            <tr>
                <th colspan="3">إجمالي المصروفات</th>
                <th colspan="2">{{ str_replace(['–', '−', '—'], '-', number_format($totalExpense, 2)) }}</th>
            </tr>
            <tr>
                <th colspan="3">صافي الربح / الخسارة</th>
                <th colspan="2">{{ str_replace(['–', '−', '—'], '-', number_format($net, 2)) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html> 