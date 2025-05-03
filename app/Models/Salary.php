<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'basic_salary', 'allowances', 'deductions', 'effective_from', 'effective_to'
    ];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
} 