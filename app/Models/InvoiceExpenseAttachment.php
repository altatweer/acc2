<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceExpenseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'voucher_id',
        'journal_entry_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceExpenseAttachmentLine::class);
    }
}




