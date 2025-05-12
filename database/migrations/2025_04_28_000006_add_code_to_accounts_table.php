<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
        });

        // محاولة حذف الفهرس القديم إذا كان موجودًا (بدون توقف الهجرة)
        try {
            DB::statement('ALTER TABLE accounts DROP INDEX accounts_code_unique');
        } catch (\Throwable $e) {
            // تجاهل الخطأ إذا لم يكن الفهرس موجودًا
        }

        // إضافة الفهرس الجديد فقط إذا كان كلا العمودين موجودين
        if (Schema::hasColumn('accounts', 'code') && Schema::hasColumn('accounts', 'currency')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->unique(['code', 'currency']);
            });
        }
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'code') && Schema::hasColumn('accounts', 'currency')) {
                $table->dropUnique(['code', 'currency']);
            }
            if (Schema::hasColumn('accounts', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
