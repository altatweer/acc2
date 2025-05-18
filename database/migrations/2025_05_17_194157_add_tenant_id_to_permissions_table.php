<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('permissions', 'tenant_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
            });
            
            // تحديث السجلات الحالية بتعيين tenant_id = 1
            DB::table('permissions')->whereNull('tenant_id')->update(['tenant_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('permissions', 'tenant_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
