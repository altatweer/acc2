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
    protected $signature = 'tenant:fix-ids {--tenant-id=1 : The tenant ID to assign to NULL records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix tenant_id for all tables - set NULL tenant_id records to specified tenant ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant-id');
        
        $this->info("🔧 بدء إصلاح tenant_id لجميع الجداول...");
        $this->info("📋 tenant_id المستهدف: {$tenantId}");
        
        // الحصول على جميع الجداول التي تحتوي على عمود tenant_id
        $tablesWithTenantId = $this->getTablesWithTenantId();
        
        if (empty($tablesWithTenantId)) {
            $this->warn("⚠️ لم يتم العثور على جداول تحتوي على عمود tenant_id");
            return;
        }
        
        $this->info("📊 تم العثور على " . count($tablesWithTenantId) . " جدول");
        
        $totalUpdated = 0;
        
        foreach ($tablesWithTenantId as $table) {
            $this->line("🔄 معالجة جدول: {$table}");
            
            try {
                // تحديث السجلات التي لها tenant_id = NULL أو مختلف عن المطلوب
                $updated = DB::table($table)
                    ->where(function($query) use ($tenantId) {
                        $query->whereNull('tenant_id')
                              ->orWhere('tenant_id', '!=', $tenantId);
                    })
                    ->update(['tenant_id' => $tenantId]);
                
                if ($updated > 0) {
                    $this->info("✅ تم تحديث {$updated} سجل في جدول {$table}");
                    $totalUpdated += $updated;
                } else {
                    $this->comment("ℹ️ جدول {$table} - لا توجد سجلات تحتاج تحديث");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ خطأ في معالجة جدول {$table}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("🎉 تم الانتهاء!");
        $this->info("📈 إجمالي السجلات المحدثة: {$totalUpdated}");
        
        // التحقق من النتائج
        $this->newLine();
        $this->info("📋 التحقق من النتائج:");
        
        foreach ($tablesWithTenantId as $table) {
            try {
                $count = DB::table($table)->where('tenant_id', $tenantId)->count();
                $nullCount = DB::table($table)->whereNull('tenant_id')->count();
                $this->line("  {$table}: {$count} سجل مع tenant_id={$tenantId}, {$nullCount} سجل NULL");
            } catch (\Exception $e) {
                $this->comment("  {$table}: تعذر التحقق");
            }
        }
        
        return 0;
    }
    
    /**
     * Get all tables that have tenant_id column
     * 
     * @return array
     */
    private function getTablesWithTenantId(): array
    {
        $database = config('database.connections.mysql.database');
        
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = ? 
            AND COLUMN_NAME = 'tenant_id'
        ", [$database]);
        
        return array_column($tables, 'TABLE_NAME');
    }
} 