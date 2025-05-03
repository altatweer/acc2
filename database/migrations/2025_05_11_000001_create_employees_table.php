<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_number')->unique();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active','inactive','terminated'])->default('active');
            $table->string('currency', 3); // IQD, USD, ...
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}; 