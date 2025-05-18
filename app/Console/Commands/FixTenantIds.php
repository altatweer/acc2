<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixTenantIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-ids {--tenant_id=1 : معرف المستأجر المراد استخدامه}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تحديث كافة السجلات التي تحتوي على tenant_id فارغ (null) وتعيين القيمة المحددة لها';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultTenantId = $this->option('tenant_id');
        
        $this->info('جاري تحديث السجلات بـ tenant_id = ' . $defaultTenantId);
        
        // الحصول على جميع الجداول في قاعدة البيانات
        $tables = DB::select('SHOW TABLES');
        $updatedTables = 0;
        $totalRecordsUpdated = 0;
        
        $this->output->progressStart(count($tables));
        
        foreach ($tables as $table) {
            $tableName = reset($table);
            
            // تخطي بعض الجداول الخاصة
            if (in_array($tableName, ['migrations', 'password_reset_tokens', 'failed_jobs'])) {
                $this->output->progressAdvance();
                continue;
            }
            
            // التحقق مما إذا كان الجدول يحتوي على عمود tenant_id
            $columns = Schema::getColumnListing($tableName);
            
            if (in_array('tenant_id', $columns)) {
                // إحصاء السجلات التي تحتاج للتحديث
                $nullCount = DB::table($tableName)->whereNull('tenant_id')->count();
                
                if ($nullCount > 0) {
                    // تحديث السجلات التي لا تحتوي على tenant_id أو قيمتها null
                    DB::table($tableName)
                        ->whereNull('tenant_id')
                        ->update(['tenant_id' => $defaultTenantId]);
                    
                    $updatedTables++;
                    $totalRecordsUpdated += $nullCount;
                    
                    $this->info("تم تحديث {$nullCount} سجلاً في جدول {$tableName}");
                }
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        
        $this->info('===== تم اكتمال العملية =====');
        $this->info("تم تحديث {$totalRecordsUpdated} سجلاً في {$updatedTables} جدول");
        
        return 0;
    }
} 