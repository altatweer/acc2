<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة شجرة الحسابات</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .print-header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        
        .print-info {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: right;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        
        .level-1 {
            background-color: #e8f4f8;
            font-weight: bold;
            font-size: 12px;
        }
        
        .level-2 {
            background-color: #f0f8ff;
            font-weight: 600;
            padding-right: 15px;
        }
        
        .level-3 {
            background-color: #fafcff;
            padding-right: 25px;
        }
        
        .level-4 {
            padding-right: 35px;
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
            font-size: 10px;
        }
        
        .type-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }
        
        .type-asset { background-color: #28a745; }
        .type-liability { background-color: #dc3545; }
        .type-equity { background-color: #6f42c1; }
        .type-revenue { background-color: #007bff; }
        .type-expense { background-color: #fd7e14; }
        
        .print-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 10px;
            break-inside: avoid;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        
        .legend {
            margin-top: 15px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-size: 9px;
            break-inside: avoid;
        }
        
        .legend ul {
            list-style-type: none;
            padding: 0;
            margin: 5px 0;
        }
        
        .legend li {
            margin: 3px 0;
        }
        
        .no-print {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- رأس الطباعة -->
    <div class="print-header">
        <h1>{{ $title }}</h1>
        <div class="print-info">
            تاريخ التوليد: {{ $generatedAt->format('Y-m-d H:i:s') }} |
            يشمل الحسابات غير النشطة: {{ $includeInactive ? 'نعم' : 'لا' }} |
            إجمالي الفئات: {{ count($accountsTree) }}
        </div>
    </div>

    <!-- جدول شجرة الحسابات -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">المستوى</th>
                <th style="width: 12%;">الكود</th>
                <th style="width: 45%;">اسم الحساب</th>
                <th style="width: 10%;">النوع</th>
                <th style="width: 10%;">نوع الحساب</th>
                <th style="width: 8%;">العملة</th>
                <th style="width: 7%;">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accountsTree as $mainCategory)
                @include('exports.partials.account_tree_row', ['node' => $mainCategory])
            @endforeach
        </tbody>
    </table>

    <!-- ملخص إحصائي -->
    @php
        $stats = [
            'total_groups' => 0,
            'total_accounts' => 0,
            'by_type' => [],
            'by_level' => [1 => 0, 2 => 0, 3 => 0, 4 => 0]
        ];
        
        function calculatePrintStats($nodes, &$stats) {
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
                    calculatePrintStats($node['children'], $stats);
                }
            }
        }
        
        calculatePrintStats($accountsTree, $stats);
    @endphp
    
    <div class="print-summary">
        <h4 style="margin: 0 0 10px 0; font-size: 12px;">ملخص شجرة الحسابات:</h4>
        
        <div class="summary-grid">
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
    <div class="legend">
        <h4 style="margin: 0 0 5px 0; font-size: 11px;">دليل مستويات شجرة الحسابات:</h4>
        <ul>
            <li><strong>المستوى 1:</strong> الفئات الرئيسية (الأصول، الخصوم، حقوق الملكية، الإيرادات، المصروفات)</li>
            <li><strong>المستوى 2:</strong> المجموعات الفرعية الأولى (مثل: النقدية والبنوك، الموردون والدائنون)</li>
            <li><strong>المستوى 3:</strong> المجموعات الفرعية الثانوية أو الحسابات المباشرة</li>
            <li><strong>المستوى 4:</strong> الحسابات التفصيلية النهائية</li>
        </ul>
    </div>

    <!-- أزرار التحكم -->
    <div class="no-print" style="position: fixed; top: 20px; left: 20px; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
            🖨️ طباعة
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
            ✖️ إغلاق
        </button>
    </div>

    <script>
        // طباعة تلقائية عند فتح الصفحة
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html> 