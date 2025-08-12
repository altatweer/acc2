<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class JournalEntryLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'debit',
        'credit',
        'currency',
        'exchange_rate'];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'exchange_rate' => 'decimal:6'];

    protected static function boot()
    {
        parent::boot();

        // حذف cache الرصيد عند إضافة أو تعديل أو حذف سطر قيد
        static::created(function ($line) {
            \Cache::forget('account_balance_' . $line->account_id);
        });

        static::updated(function ($line) {
            \Cache::forget('account_balance_' . $line->account_id);
        });

        static::deleted(function ($line) {
            \Cache::forget('account_balance_' . $line->account_id);
        });
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
} 