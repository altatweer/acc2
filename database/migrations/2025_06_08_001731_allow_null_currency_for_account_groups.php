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
        // تعديل عمود currency ليقبل NULL للفئات (is_group = true)
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('currency', 3)->nullable()->change();
        });
        
        // تحديث الفئات الموجودة لتصبح NULL
        DB::table('accounts')
            ->where('is_group', true)
            ->update(['currency' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة تعيين العملة الافتراضية للفئات
        DB::table('accounts')
            ->where('is_group', true)
            ->whereNull('currency')
            ->update(['currency' => 'IQD']);
            
        // إعادة العمود إلى غير nullable مع القيمة الافتراضية
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('currency', 3)->default('IQD')->change();
        });
    }
};
