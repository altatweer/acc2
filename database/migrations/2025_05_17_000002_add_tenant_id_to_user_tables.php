<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // إضافة tenant_id لجدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
        });

        // إضافة tenant_id لجدول الأدوار
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->index()->after('id');
        });

        // إضافة tenant_id لجدول ربط المستخدمين بالأدوار
        // model_has_roles هو جدول بولي مورفيك، لذا نضيف tenant_id كعمود منفصل
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });

        if (Schema::hasTable('model_has_roles') && Schema::hasColumn('model_has_roles', 'tenant_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
}; 