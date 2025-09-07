<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة عمود salary المفقود إلى جدول employees
     */
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 18, 2)->default(0)->after('status')->comment('الراتب الأساسي');
                }
            });
            
            // تحديث البيانات الموجودة
            \DB::statement("UPDATE employees SET salary = COALESCE(base_salary, 0) WHERE salary IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('salary');
            });
        }
    }
};