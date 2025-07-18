<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 تقرير شامل لفحص tenant_id في جميع الجداول\n";
echo "==============================================\n\n";

// قائمة بجميع الجداول في النظام
$tables = [
    'users',
    'accounts', 
    'currencies',
    'transactions',
    'vouchers',
    'journal_entries',
    'journal_entry_lines',
    'account_balances',
    'invoices',
    'invoice_items',
    'customers',
    'employees',
    'items',
    'salary_batches',
    'salary_payments',
    'branches',
    'currency_rates',
    'multi_currency_transactions',
    'item_prices',
    'customer_balances',
    'exchange_rate_history'
];

$totalTables = 0;
$tablesWithTenantId = 0;
$tablesWithData = 0;
$totalRecords = 0;
$recordsWithTenant1 = 0;
$recordsWithNullTenant = 0;
$recordsWithOtherTenant = 0;

echo "📊 نتائج الفحص:\n\n";

foreach($tables as $table) {
    $totalTables++;
    
    try {
        // فحص إذا كان الجدول موجود
        if (!Schema::hasTable($table)) {
            echo "❌ $table: الجدول غير موجود\n";
            continue;
        }
        
        // فحص إذا كان عمود tenant_id موجود
        $hasTenantColumn = Schema::hasColumn($table, 'tenant_id');
        
        if (!$hasTenantColumn) {
            $count = DB::table($table)->count();
            echo "⚠️  $table: لا يحتوي على عمود tenant_id ($count سجل)\n";
            continue;
        }
        
        $tablesWithTenantId++;
        
        // إحصائيات الجدول
        $total = DB::table($table)->count();
        
        if ($total == 0) {
            echo "⚪ $table: فارغ (0 سجل)\n";
            continue;
        }
        
        $tablesWithData++;
        $totalRecords += $total;
        
        $tenant1 = DB::table($table)->where('tenant_id', 1)->count();
        $nullTenant = DB::table($table)->whereNull('tenant_id')->count();
        $otherTenant = $total - $tenant1 - $nullTenant;
        
        $recordsWithTenant1 += $tenant1;
        $recordsWithNullTenant += $nullTenant;
        $recordsWithOtherTenant += $otherTenant;
        
        if ($tenant1 == $total) {
            echo "✅ $table: جميع السجلات ($total) لها tenant_id = 1\n";
        } else {
            echo "⚠️  $table: إجمالي $total | tenant_id=1: $tenant1 | null: $nullTenant | أخرى: $otherTenant\n";
        }
        
    } catch(Exception $e) {
        echo "❌ $table: خطأ - " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📈 ملخص التقرير:\n";
echo "=============\n\n";

echo "📊 إجمالي الجداول المفحوصة: $totalTables\n";
echo "✅ الجداول التي تحتوي على tenant_id: $tablesWithTenantId\n";
echo "📦 الجداول التي تحتوي على بيانات: $tablesWithData\n";
echo "📄 إجمالي السجلات: $totalRecords\n";
echo "✅ السجلات مع tenant_id = 1: $recordsWithTenant1\n";
echo "⚪ السجلات مع tenant_id = null: $recordsWithNullTenant\n";
echo "⚠️  السجلات مع tenant_id أخرى: $recordsWithOtherTenant\n\n";

// حساب النسب المئوية
if ($totalRecords > 0) {
    $percentTenant1 = round(($recordsWithTenant1 / $totalRecords) * 100, 2);
    $percentNull = round(($recordsWithNullTenant / $totalRecords) * 100, 2);
    $percentOther = round(($recordsWithOtherTenant / $totalRecords) * 100, 2);
    
    echo "📈 النسب المئوية:\n";
    echo "   tenant_id = 1: $percentTenant1%\n";
    echo "   tenant_id = null: $percentNull%\n";
    echo "   tenant_id أخرى: $percentOther%\n\n";
}

// تقييم حالة النظام
echo "🎯 تقييم النظام:\n";
if ($recordsWithTenant1 == $totalRecords && $totalRecords > 0) {
    echo "✅ ممتاز: جميع السجلات في النظام لها tenant_id = 1\n";
    echo "🎉 النظام مهيأ بشكل صحيح للـ multi-tenancy\n";
} elseif ($recordsWithNullTenant > 0 || $recordsWithOtherTenant > 0) {
    echo "⚠️  تحذير: يوجد سجلات بدون tenant_id صحيح\n";
    echo "🔧 يحتاج إصلاح لضمان سلامة البيانات\n";
    
    if ($recordsWithNullTenant > 0) {
        echo "   - $recordsWithNullTenant سجل لها tenant_id = null\n";
    }
    if ($recordsWithOtherTenant > 0) {
        echo "   - $recordsWithOtherTenant سجل لها tenant_id غير 1\n";
    }
} else {
    echo "✅ جيد: النظام في حالة سليمة\n";
}

echo "\n💡 توصيات:\n";
echo "1. تأكد من أن جميع السجلات الجديدة تحصل على tenant_id = 1 تلقائياً\n";
echo "2. راجع الـ middleware للتأكد من تطبيق tenant_id في جميع العمليات\n";
echo "3. اختبر إنشاء سجلات جديدة للتأكد من إضافة tenant_id تلقائياً\n";

echo "\n🏁 انتهى التقرير\n"; 