<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('accounting_settings');
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // اسم الإعداد الافتراضي (مثال: default_sales_account)
            $table->string('value'); // قيمة الإعداد (عادة id الحساب)
            $table->string('currency', 3)->nullable(); // العملة (اختياري)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
