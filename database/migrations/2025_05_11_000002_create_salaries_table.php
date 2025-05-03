<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('basic_salary', 18, 2);
            $table->json('allowances')->nullable();
            $table->json('deductions')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}; 