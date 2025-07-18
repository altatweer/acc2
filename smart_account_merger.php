<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\JournalEntryLine;

class SmartAccountMerger extends Command
{
    protected $signature = 'accounts:smart-merge 
                            {--dry-run : Preview changes without executing}
                            {--backup : Create backup before executing}';

    protected $description = 'دمج الحسابات المتشابهة إلى حسابات متعددة العملات';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $backup = $this->option('backup');

        $this->info('🚀 بدء عملية الدمج الذكي للحسابات');
        
        if ($backup) {
            $this->createBackup();
        }

        // 1. تحليل الحسابات المكررة
        $duplicateGroups = $this->findDuplicateAccounts();
        
        $this->table(['المجموعة', 'اسم الحساب', 'العملات', 'عدد المعاملات'], 
                     $this->formatDuplicatesTable($duplicateGroups));

        if (!$this->confirm('هل تريد المتابعة مع الدمج؟')) {
            return;
        }

        // 2. تنفيذ الدمج
        foreach ($duplicateGroups as $groupName => $accounts) {
            $this->mergeAccountGroup($accounts, $dryRun);
        }

        $this->info('✅ تم إنجاز عملية الدمج بنجاح!');
    }

    /**
     * البحث عن الحسابات المكررة بناء على الاسم
     */
    private function findDuplicateAccounts(): array
    {
        $accounts = Account::where('is_group', false)
                          ->where('tenant_id', 1)
                          ->get()
                          ->groupBy(function($account) {
                              // تجميع حسب الاسم بعد إزالة العملة
                              $name = $account->name;
                              $name = preg_replace('/\s*(USD|IQD|EUR|GBP|\$|د\.ع|€|£)\s*/', '', $name);
                              $name = trim($name);
                              return $name;
                          });

        // فلترة المجموعات التي تحتوي على أكثر من حساب
        return $accounts->filter(function($group) {
            return $group->count() > 1;
        })->toArray();
    }

    /**
     * تنسيق جدول المعاينة
     */
    private function formatDuplicatesTable($duplicateGroups): array
    {
        $table = [];
        foreach ($duplicateGroups as $groupName => $accounts) {
            $currencies = collect($accounts)->pluck('currency')->join(', ');
            $totalTransactions = collect($accounts)->sum(function($account) {
                return $account->journalEntryLines()->count();
            });
            
            $table[] = [
                'المجموعة' => $groupName,
                'اسم الحساب' => collect($accounts)->pluck('name')->join(' | '),
                'العملات' => $currencies,
                'عدد المعاملات' => $totalTransactions
            ];
        }
        return $table;
    }

    /**
     * دمج مجموعة من الحسابات
     */
    private function mergeAccountGroup($accounts, $dryRun = false): void
    {
        $mainAccount = collect($accounts)->first();
        $groupName = $this->cleanAccountName($mainAccount->name);
        
        $this->info("🔄 دمج مجموعة: {$groupName}");

        if ($dryRun) {
            $this->line("   [معاينة] سيتم إنشاء حساب متعدد العملات");
            return;
        }

        DB::transaction(function() use ($accounts, $groupName, $mainAccount) {
            
            // 1. إنشاء الحساب الجديد متعدد العملات
            $newAccount = Account::create([
                'name' => $groupName,
                'code' => $this->generateNewCode($mainAccount),
                'parent_id' => $mainAccount->parent_id,
                'type' => $mainAccount->type,
                'nature' => $mainAccount->nature,
                'is_group' => false,
                'is_cash_box' => $mainAccount->is_cash_box,
                'is_multi_currency' => true,
                'default_currency' => 'IQD',
                'supported_currencies' => $this->getSupportedCurrencies($accounts),
                'tenant_id' => 1
            ]);

            $this->info("   ✅ تم إنشاء حساب جديد: {$newAccount->name} (ID: {$newAccount->id})");

            // 2. تحديث جميع المعاملات
            $totalUpdated = 0;
            foreach ($accounts as $oldAccount) {
                $updated = JournalEntryLine::where('account_id', $oldAccount->id)
                                         ->update(['account_id' => $newAccount->id]);
                $totalUpdated += $updated;
                
                $this->line("   🔄 نقل {$updated} معاملة من {$oldAccount->name}");
                
                // 3. حذف الحساب القديم
                $oldAccount->delete();
                $this->line("   🗑️ تم حذف الحساب القديم: {$oldAccount->name}");
            }

            $this->info("   📊 إجمالي المعاملات المنقولة: {$totalUpdated}");
        });
    }

    /**
     * تنظيف اسم الحساب من العملات
     */
    private function cleanAccountName($name): string
    {
        $cleanName = preg_replace('/\s*(USD|IQD|EUR|GBP|\$|د\.ع|€|£)\s*/', '', $name);
        return trim($cleanName);
    }

    /**
     * توليد كود جديد للحساب
     */
    private function generateNewCode($baseAccount): string
    {
        $baseCode = preg_replace('/[A-Z]{3}$/', '', $baseAccount->code);
        return $baseCode . 'MC'; // MC = Multi Currency
    }

    /**
     * الحصول على العملات المدعومة
     */
    private function getSupportedCurrencies($accounts): string
    {
        $currencies = collect($accounts)->pluck('currency')->unique()->values();
        return json_encode($currencies->toArray());
    }

    /**
     * إنشاء نسخة احتياطية
     */
    private function createBackup(): void
    {
        $this->info('📦 إنشاء نسخة احتياطية...');
        
        $backupFile = storage_path('app/backups/accounts_backup_' . date('Y_m_d_H_i_s') . '.sql');
        
        // إنشاء مجلد النسخ الاحتياطية إذا لم يكن موجوداً
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s accounts journal_entry_lines transactions vouchers > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $backupFile
        );

        exec($command);
        $this->info("✅ تم حفظ النسخة الاحتياطية: {$backupFile}");
    }
} 