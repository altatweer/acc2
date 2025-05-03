<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    protected $fillable = [
        'currency',
        'sales_account_id',
        'purchases_account_id',
        'receivables_account_id',
        'payables_account_id',
        'expenses_account_id',
        'liabilities_account_id',
        'deductions_account_id',
    ];

    public function salesAccount() { return $this->belongsTo(Account::class, 'sales_account_id'); }
    public function purchasesAccount() { return $this->belongsTo(Account::class, 'purchases_account_id'); }
    public function receivablesAccount() { return $this->belongsTo(Account::class, 'receivables_account_id'); }
    public function payablesAccount() { return $this->belongsTo(Account::class, 'payables_account_id'); }
    public function expensesAccount() { return $this->belongsTo(Account::class, 'expenses_account_id'); }
    public function liabilitiesAccount() { return $this->belongsTo(Account::class, 'liabilities_account_id'); }
    public function deductionsAccount() { return $this->belongsTo(Account::class, 'deductions_account_id'); }
} 