<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceExpenseAttachmentLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_expense_attachment_id',
        'cash_account_id',
        'expense_account_id',
        'amount',
        'currency',
        'exchange_rate',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
    ];

    public function attachment()
    {
        return $this->belongsTo(InvoiceExpenseAttachment::class, 'invoice_expense_attachment_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }
}




