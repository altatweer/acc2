<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الميزانية العمومية</title>
    <style>
        /* CSS Variables from Print Settings */
        :root {
            @if(isset($printSettings))
                @php
                    $cssVars = $printSettings->getCssVariables();
                    foreach($cssVars as $key => $value) {
                        echo $key . ': ' . $value . ";\n            ";
                    }
                @endphp
            @else
                --primary-color: #1976d2;
                --secondary-color: #1565c0;
                --accent-color: #42a5f5;
                --background-color: #f5f5f5;
                --text-color: #212121;
            @endif
        }
        
        * {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background: var(--background-color);
            color: var(--text-color);
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header .date-range {
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 25px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section-header {
            background: var(--accent-color);
            color: white;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .section-content {
            padding: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        th, td {
            padding: 10px 15px;
            text-align: right;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: var(--background-color);
            font-weight: bold;
            color: var(--text-color);
        }
        
        tr:nth-child(even) {
            background: rgba(var(--primary-color-rgb), 0.02);
        }
        
        .amount {
            text-align: left;
            font-weight: bold;
        }
        
        .positive {
            color: #2e7d32;
        }
        
        .negative {
            color: #c62828;
        }
        
        .section-total {
            background: var(--primary-color);
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .section-total td {
            border-bottom: none;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 15px;
            background: var(--background-color);
            border-radius: 8px;
            font-size: 12px;
            color: #666;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    @if(isset($printSettings))
        <div class="company-info">
            @if($printSettings->company_logo)
                <img src="{{ public_path('storage/' . $printSettings->company_logo) }}" alt="شعار الشركة" class="company-logo">
            @endif
            <h2 style="color: var(--primary-color); margin: 0;">{{ $printSettings->company_name ?: 'نظام المحاسبة المتكامل' }}</h2>
            @if($printSettings->company_address)
                <p style="margin: 5px 0;">{{ $printSettings->company_address }}</p>
            @endif
        </div>
    @endif
    
    <div class="header">
        <h1>الميزانية العمومية</h1>
        @if($from || $to)
            <div class="date-range">
                الفترة من {{ $from ? \Carbon\Carbon::parse($from)->format('Y/m/d') : 'البداية' }} 
                إلى {{ $to ? \Carbon\Carbon::parse($to)->format('Y/m/d') : 'النهاية' }}
            </div>
        @endif
    </div>

    @foreach($sections as $sectionName => $sectionData)
        <div class="section">
            <div class="section-header">
                {{ $sectionName }}
            </div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">رمز الحساب</th>
                            <th style="width: 55%;">اسم الحساب</th>
                            <th style="width: 30%;">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectionData['rows'] as $row)
                            @if($row['balance'] != 0)
                                <tr>
                                    <td>{{ $row['account']->code }}</td>
                                    <td>{{ $row['account']->name }}</td>
                                    <td class="amount {{ $row['balance'] >= 0 ? 'positive' : 'negative' }}">
                                        {{ number_format(abs($row['balance']), 2) }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="section-total">
                            <td colspan="2">إجمالي {{ $sectionName }}</td>
                            <td class="amount">{{ number_format(abs($sectionData['total']), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

    <div class="footer">
        <p>تم إنشاء التقرير في {{ now()->format('Y/m/d H:i') }}</p>
        @if(isset($printSettings) && $printSettings->company_name)
            <p>{{ $printSettings->company_name }}</p>
        @endif
    </div>
</body>
</html> 