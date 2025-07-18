<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:clean {--force : Force the operation without confirmation}';

    /**
     * The description of the console command.
     */
    protected $description = 'Clean all system data and prepare for fresh start (تنظيف جميع بيانات النظام للبدء من الصفر)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  تحذير: هذا الأمر سيحذف جميع البيانات من النظام!');
            $this->warn('⚠️  WARNING: This command will delete ALL data from the system!');
            
            if (!$this->confirm('هل أنت متأكد من أنك تريد المتابعة؟ Are you sure you want to continue?')) {
                $this->info('تم إلغاء العملية. Operation cancelled.');
                return;
            }
        }

        $this->info('🧹 بدء تنظيف النظام... Starting system cleanup...');

        // تعطيل فحص المفاتيح الخارجية مؤقتاً
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // قائمة الجداول المراد تنظيفها (بالترتيب الصحيح)
        $tables = [
            'journal_entry_lines',
            'journal_entries', 
            'transactions',
            'account_balances',
            'salary_payments',
            'salary_batches',
            'salaries',
            'invoice_items',
            'invoices',
            'vouchers',
            'account_user',
            'accounts',
            'customers',
            'employees',
            'items',
            'accounting_settings',
            'settings',
            'branches',
        ];

        $cleanedTables = 0;
        $totalRecords = 0;

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $totalRecords += $count;
                
                if ($count > 0) {
                    DB::table($table)->truncate();
                    $this->info("✅ تم تنظيف جدول {$table} ({$count} سجل)");
                    $cleanedTables++;
                } else {
                    $this->line("⏭️  جدول {$table} فارغ بالفعل");
                }
            } else {
                $this->warn("⚠️  جدول {$table} غير موجود");
            }
        }

        // إعادة تفعيل فحص المفاتيح الخارجية
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // إعادة تعيين auto increment للجداول المهمة
        $resetTables = ['accounts', 'currencies', 'vouchers', 'transactions', 'journal_entries'];
        foreach ($resetTables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            }
        }

        $this->info("🎉 تم تنظيف النظام بنجاح!");
        $this->info("📊 الإحصائيات:");
        $this->line("   - عدد الجداول المنظفة: {$cleanedTables}");
        $this->line("   - إجمالي السجلات المحذوفة: {$totalRecords}");
        $this->line("   - تم إعادة تعيين auto increment للجداول الرئيسية");
        
        $this->info("\n✨ النظام جاهز الآن للبدء من الصفر!");
        $this->info("💡 الخطوة التالية: تشغيل php artisan db:seed لإنشاء البيانات الأساسية");
    }
} 