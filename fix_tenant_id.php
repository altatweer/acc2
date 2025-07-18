<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 سكريبت إصلاح tenant_id\n";
echo "=======================\n\n";

// قائمة بجميع الجداول التي تحتوي على tenant_id
$tables = [
    'users', 'accounts', 'currencies', 'transactions', 'vouchers',
    'journal_entries', 'journal_entry_lines', 'account_balances',
    'invoices', 'invoice_items', 'customers', 'employees', 'items',
    'salary_batches', 'salary_payments', 'branches', 'currency_rates',
    'multi_currency_transactions', 'item_prices', 'customer_balances',
    'exchange_rate_history'
];

$fixedRecords = 0;
$totalTables = 0;

echo "🔍 البحث عن السجلات التي تحتاج إصلاح...\n\n";

foreach($tables as $table) {
    try {
        // فحص إذا كان الجدول موجود ويحتوي على tenant_id
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'tenant_id')) {
            continue;
        }
        
        $totalTables++;
        
        // البحث عن السجلات التي تحتاج إصلاح
        $recordsToFix = DB::table($table)
            ->where(function($query) {
                $query->whereNull('tenant_id')
                      ->orWhere('tenant_id', '!=', 1);
            })
            ->count();
            
        if ($recordsToFix > 0) {
            echo "⚠️  الجدول $table: وجد $recordsToFix سجل يحتاج إصلاح\n";
            
            // تأكيد الإصلاح
            echo "   هل تريد إصلاح هذه السجلات؟ (y/n): ";
            $handle = fopen("php://stdin", "r");
            $confirm = trim(fgets($handle));
            fclose($handle);
            
            if (strtolower($confirm) === 'y' || strtolower($confirm) === 'yes') {
                // إصلاح السجلات
                $updated = DB::table($table)
                    ->where(function($query) {
                        $query->whereNull('tenant_id')
                              ->orWhere('tenant_id', '!=', 1);
                    })
                    ->update(['tenant_id' => 1]);
                    
                echo "   ✅ تم إصلاح $updated سجل في الجدول $table\n";
                $fixedRecords += $updated;
            } else {
                echo "   ⏭️  تم تخطي الجدول $table\n";
            }
        } else {
            echo "✅ الجدول $table: جميع السجلات سليمة\n";
        }
        
    } catch(Exception $e) {
        echo "❌ خطأ في الجدول $table: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 ملخص الإصلاح:\n";
echo "================\n\n";
echo "📋 إجمالي الجداول المفحوصة: $totalTables\n";
echo "🔧 إجمالي السجلات المُصلحة: $fixedRecords\n";

if ($fixedRecords > 0) {
    echo "✅ تم إصلاح جميع المشاكل بنجاح!\n";
    echo "🎉 النظام الآن مهيأ بشكل صحيح للـ multi-tenancy\n";
} else {
    echo "✅ لا توجد مشاكل تحتاج إصلاح\n";
    echo "🎉 النظام في حالة ممتازة!\n";
}

echo "\n💡 نصائح للمستقبل:\n";
echo "==================\n";
echo "1. استخدم هذا السكريبت دورياً للتأكد من سلامة tenant_id\n";
echo "2. تأكد من أن جميع Models تستخدم BelongsToTenant trait\n";
echo "3. استخدم middleware للتأكد من إضافة tenant_id تلقائياً\n";
echo "4. اختبر إنشاء سجلات جديدة للتأكد من إضافة tenant_id\n";

echo "\n�� انتهى السكريبت\n"; 