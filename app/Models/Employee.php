<?php
namespace App\Models;

use App\Traits\BelongsToTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name', 'employee_number', 'department', 'job_title', 'hire_date', 'status', 'currency',
        'tenant_id',
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