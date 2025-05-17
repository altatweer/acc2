<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Account extends Model
{
    use HasFactory, BelongsToTenant;

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

    // علاقة متكررة لجلب جميع الأبناء بشكل هرمي
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
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

    public function balance()
    {
        // مجموع المدين والدائن
        $debit = $this->journalEntryLines()->sum('debit');
        $credit = $this->journalEntryLines()->sum('credit');
        // إذا كانت طبيعة الحساب مدين: الرصيد = المدين - الدائن
        // إذا كانت طبيعة الحساب دائن: الرصيد = الدائن - المدين
        if ($this->nature === 'مدين' || $this->nature === 'debit') {
            return $debit - $credit;
        } else {
            return $credit - $debit;
        }
    }

    public function canWithdraw($amount)
    {
        return $this->balance() >= $amount;
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'account_user', 'account_id', 'user_id');
    }
}
