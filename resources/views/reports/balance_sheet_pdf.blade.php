<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ميزان المراجعة</title>
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
    <h2>ميزان المراجعة (Balance Sheet)</h2>
    <div style="margin-bottom: 10px; text-align: center;">
        @if($from && $to)
            <span>الفترة: من {{ $from }} إلى {{ $to }}</span>
        @endif
    </div>
    <div style="display: flex; gap: 10px;">
        <div style="flex:1;">
            <h4>الأصول</h4>
            <table>
                <tbody>
                @foreach($sections['أصل']['rows'] as $row)
                    <tr>
                        <td>{{ $row['account']->name }}</td>
                        <td>{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>الإجمالي</th>
                        <th>{{ number_format($sections['أصل']['total'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="flex:1;">
            <h4>الخصوم</h4>
            <table>
                <tbody>
                @foreach($sections['خصم']['rows'] as $row)
                    <tr>
                        <td>{{ $row['account']->name }}</td>
                        <td>{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>الإجمالي</th>
                        <th>{{ number_format($sections['خصم']['total'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="flex:1;">
            <h4>حقوق الملكية</h4>
            <table>
                <tbody>
                @foreach($sections['حقوق ملكية']['rows'] as $row)
                    <tr>
                        <td>{{ $row['account']->name }}</td>
                        <td>{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>الإجمالي</th>
                        <th>{{ number_format($sections['حقوق ملكية']['total'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html> 