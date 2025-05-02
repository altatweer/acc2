<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',         // اسم الحساب أو الفئة
        'code',         // رقم الحساب المحاسبي
        'parent_id',    // الحساب الأب
        'type',         // نوع الحساب المحاسبي (أصل، خصم، إيراد، مصروف، رأس مال)
        'nature',       // طبيعة الحساب (مدين/دائن)
        'is_group',     // هل هو فئة تصنيفية أو حساب فعلي
        'is_cash_box',  // هل هو صندوق كاش
        'currency',     // رمز العملة للحساب
    ];

    protected $casts = [
        'is_group'    => 'boolean',
        'is_cash_box' => 'boolean',
    ];

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get the account balances for this account.
     */
    public function balances()
    {
        return $this->hasMany(AccountBalance::class, 'account_id');
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function scopeGroups($query)
    {
        return $query->where('is_group', true);
    }

    public function scopeAccounts($query)
    {
        return $query->where('is_group', false);
    }

    /**
     * Get all transactions for this account.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'account_id');
    }

    public function journalEntryLines()
    {
        return $this->hasMany(\App\Models\JournalEntryLine::class, 'account_id');
    }
}
