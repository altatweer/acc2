<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_batch_id',
        'employee_id', 
        'salary_month', 
        'gross_salary', 
        'total_allowances', 
        'total_deductions', 
        'net_salary', 
        'currency',
        'exchange_rate',
        'base_currency_net_salary',
        'payment_date', 
        'status', 
        'journal_entry_id', 
        'voucher_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function salaryBatch()
    {
        return $this->belongsTo(SalaryBatch::class, 'salary_batch_id');
    }
} 