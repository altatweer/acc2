<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_batch_id')->nullable()->constrained('salary_batches')->onDelete('set null');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('salary_month', 7); // مثال: 2025-05
            $table->decimal('gross_salary', 18, 2);
            $table->decimal('total_allowances', 18, 2)->default(0);
            $table->decimal('total_deductions', 18, 2)->default(0);
            $table->decimal('net_salary', 18, 2);
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending','paid','cancelled'])->default('pending');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('salary_payments');
    }
}; 