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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // رقم الفاتورة
            $table->unsignedBigInteger('account_id'); // عميل الفاتورة
            $table->date('date'); // تاريخ الإنشاء
            $table->decimal('total', 15, 2); // إجمالي الفاتورة
            $table->string('currency', 3)->default('IQD'); // عملة الفاتورة
            $table->decimal('exchange_rate', 15, 6)->default(1); // سعر الصرف مقابل العملة الافتراضية
            $table->enum('status', ['unpaid','partial','paid'])->default('unpaid'); // حالة السداد
            $table->unsignedBigInteger('created_by'); // المحاسب
            $table->timestamps();

            // العلاقات الخارجية
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
