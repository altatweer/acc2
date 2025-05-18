<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixMissingTenantIds extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        // هذا الملف يقوم بتحديث جميع السجلات التي لا تحتوي على tenant_id
        // ويعين لها القيمة الافتراضية 1
        
        // الحصول على جميع الجداول في قاعدة البيانات
        $tables = DB::select('SHOW TABLES');
        
        foreach ($tables as $table) {
            $tableName = reset($table);
            
            // تخطي بعض الجداول الخاصة
            if (in_array($tableName, ['migrations', 'password_reset_tokens', 'failed_jobs'])) {
                continue;
            }
            
            // التحقق مما إذا كان الجدول يحتوي على عمود tenant_id
            $columns = Schema::getColumnListing($tableName);
            
            if (in_array('tenant_id', $columns)) {
                // تحديث السجلات التي لا تحتوي على tenant_id أو قيمتها null
                DB::table($tableName)
                    ->whereNull('tenant_id')
                    ->update(['tenant_id' => 1]);
            }
        }
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        // لا حاجة لعكس هذه العملية
    }
} 