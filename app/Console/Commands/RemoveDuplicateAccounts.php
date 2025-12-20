<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class RemoveDuplicateAccounts extends Command
{
    protected $signature = 'accounts:remove-duplicates {--dry-run : عرض التكرارات فقط دون حذف}';
    protected $description = 'إزالة الحسابات المكررة بناءً على code';

    public function handle()
    {
        $this->info('🔍 بدء البحث عن الحسابات المكررة...');
        
        // البحث عن الحسابات المكررة بناءً على code
        $duplicates = DB::table('accounts')
            ->select('code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('code')
            ->groupBy('code')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->info('✅ لا توجد حسابات مكررة!');
            return 0;
        }
        
        $this->warn("⚠️  تم العثور على {$duplicates->count()} حساب مكرر");
        
        $totalToDelete = 0;
        $totalToKeep = 0;
        
        foreach ($duplicates as $duplicate) {
            $accounts = Account::where('code', $duplicate->code)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // الاحتفاظ بأول حساب (الأقدم) وحذف الباقي
            $keepAccount = $accounts->first();
            $deleteAccounts = $accounts->skip(1);
            
            $this->line("\n📋 الكود: {$duplicate->code} - الاسم: {$keepAccount->name}");
            $this->line("   ✅ سيتم الاحتفاظ بـ: ID {$keepAccount->id} (تم الإنشاء: {$keepAccount->created_at})");
            
            foreach ($deleteAccounts as $account) {
                $this->line("   ❌ سيتم حذف: ID {$account->id} (تم الإنشاء: {$account->created_at})");
                $totalToDelete++;
            }
            
            $totalToKeep++;
            
            if (!$this->option('dry-run')) {
                // التحقق من وجود معاملات مرتبطة
                $hasTransactions = DB::table('journal_entry_lines')
                    ->whereIn('account_id', $deleteAccounts->pluck('id'))
                    ->exists();
                
                if ($hasTransactions) {
                    $this->error("   ⚠️  تحذير: الحساب ID {$account->id} له معاملات مرتبطة!");
                    // نقل المعاملات إلى الحساب المحفوظ
                    DB::table('journal_entry_lines')
                        ->whereIn('account_id', $deleteAccounts->pluck('id'))
                        ->update(['account_id' => $keepAccount->id]);
                    $this->info("   ✅ تم نقل المعاملات إلى الحساب المحفوظ");
                }
                
                // حذف الحسابات المكررة
                foreach ($deleteAccounts as $account) {
                    $account->delete();
                }
            }
        }
        
        if ($this->option('dry-run')) {
            $this->info("\n📊 ملخص (Dry Run):");
            $this->info("   - حسابات سيتم الاحتفاظ بها: {$totalToKeep}");
            $this->info("   - حسابات سيتم حذفها: {$totalToDelete}");
            $this->warn("\n⚠️  هذا كان dry-run. لتطبيق التغييرات، قم بتشغيل الأمر بدون --dry-run");
        } else {
            $this->info("\n✅ تم حذف {$totalToDelete} حساب مكرر");
            $this->info("✅ تم الاحتفاظ بـ {$totalToKeep} حساب");
            
            // التحقق من النتيجة
            $remainingDuplicates = DB::table('accounts')
                ->select('code', DB::raw('COUNT(*) as count'))
                ->whereNotNull('code')
                ->groupBy('code')
                ->having('count', '>', 1)
                ->count();
            
            if ($remainingDuplicates == 0) {
                $this->info("✅ تم إزالة جميع التكرارات بنجاح!");
            } else {
                $this->warn("⚠️  لا يزال هناك {$remainingDuplicates} تكرار متبقي");
            }
        }
        
        return 0;
    }
}

