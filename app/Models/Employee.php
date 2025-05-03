<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'employee_number', 'department', 'job_title', 'hire_date', 'status', 'currency'
    ];

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
} 