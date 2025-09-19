<?php

// إصلاح إجماليات القيود المحاسبية الموجودة
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

echo "🔧 بدء إصلاح إجماليات القيود المحاسبية...\n";

try {
    $journalEntries = JournalEntry::with('lines')->get();
    
    echo "📊 تم العثور على " . $journalEntries->count() . " قيد محاسبي\n";
    
    $updatedCount = 0;
    
    foreach ($journalEntries as $entry) {
        $totalDebit = $entry->lines->sum('debit');
        $totalCredit = $entry->lines->sum('credit');
        
        // تحديث فقط إذا كانت الإجماليات مختلفة
        if ($entry->total_debit != $totalDebit || $entry->total_credit != $totalCredit) {
            $entry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit
            ]);
            
            echo "✅ تم تحديث القيد #{$entry->id}: مدين {$totalDebit}, دائن {$totalCredit}\n";
            $updatedCount++;
        }
    }
    
    echo "\n🎉 تم الانتهاء! تم تحديث {$updatedCount} قيد من أصل " . $journalEntries->count() . " قيد\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}
