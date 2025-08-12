<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            background-color: #1f4e79;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #4472c4;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .level-1 {
            background-color: #e8f1ff;
            font-weight: bold;
            font-size: 14px;
        }
        .level-2 {
            background-color: #f2f7ff;
            font-weight: 600;
            padding-right: 20px;
        }
        .level-3 {
            background-color: #fafcff;
            padding-right: 40px;
        }
        .level-4 {
            padding-right: 60px;
            font-style: italic;
        }
        .account-group {
            color: #2c5aa0;
            font-weight: bold;
        }
        .account-item {
            color: #333;
        }
        .code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .type-asset { background-color: #28a745; }
        .type-liability { background-color: #dc3545; }
        .type-equity { background-color: #6f42c1; }
        .type-revenue { background-color: #007bff; }
        .type-expense { background-color: #fd7e14; }
    </style>
</head>
<body>
    <!-- رأس التقرير -->
    <div class="header">
        {{ $title }}
    </div>
    
    <!-- معلومات التقرير -->
    <div class="info">
        <strong>تاريخ التوليد:</strong> {{ $generatedAt->format('Y-m-d H:i:s') }}<br>
        <strong>إجمالي الفئات الرئيسية:</strong> {{ count($accountsTree) }}
    </div>

    <!-- جدول شجرة الحسابات -->
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">المستوى</th>
                <th style="width: 15%;">الكود</th>
                <th style="width: 45%;">اسم الحساب</th>
                <th style="width: 12%;">النوع</th>
                <th style="width: 12%;">نوع الحساب</th>
                <th style="width: 6%;">العملة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accountsTree as $mainCategory)
                @include('exports.partials.account_tree_row', ['node' => $mainCategory])
            @endforeach
        </tbody>
    </table>

    <!-- ملخص إحصائي -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h4>ملخص شجرة الحسابات:</h4>
        
        @php
            $stats = [
                'total_groups' => 0,
                'total_accounts' => 0,
                'by_type' => [],
                'by_level' => [1 => 0, 2 => 0, 3 => 0, 4 => 0]
            ];
            
            function calculateStats($nodes, &$stats) {
                foreach($nodes as $node) {
                    if($node['account']->is_group) {
                        $stats['total_groups']++;
                    } else {
                        $stats['total_accounts']++;
                    }
                    
                    $type = $node['account']->type ?? 'غير محدد';
                    $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
                    $stats['by_level'][$node['level']]++;
                    
                    if(!empty($node['children'])) {
                        calculateStats($node['children'], $stats);
                    }
                }
            }
            
            calculateStats($accountsTree, $stats);
        @endphp
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 10px;">
            <div>
                <strong>إجمالي المجموعات:</strong> {{ $stats['total_groups'] }}<br>
                <strong>إجمالي الحسابات:</strong> {{ $stats['total_accounts'] }}
            </div>
            
            <div>
                <strong>حسب النوع:</strong><br>
                @foreach($stats['by_type'] as $type => $count)
                    • {{ $type }}: {{ $count }}<br>
                @endforeach
            </div>
            
            <div>
                <strong>حسب المستوى:</strong><br>
                @foreach($stats['by_level'] as $level => $count)
                    @if($count > 0)
                        • المستوى {{ $level }}: {{ $count }}<br>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- دليل المستويات -->
    <div style="margin-top: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px;">
        <h4>دليل مستويات شجرة الحسابات:</h4>
        <ul style="list-style-type: none; padding: 0;">
            <li><strong>المستوى 1:</strong> الفئات الرئيسية (الأصول، الخصوم، حقوق الملكية، الإيرادات، المصروفات)</li>
            <li><strong>المستوى 2:</strong> المجموعات الفرعية الأولى (مثل: النقدية والبنوك، الموردون والدائنون)</li>
            <li><strong>المستوى 3:</strong> المجموعات الفرعية الثانوية أو الحسابات المباشرة</li>
            <li><strong>المستوى 4:</strong> الحسابات التفصيلية النهائية</li>
        </ul>
    </div>
</body>
</html> 