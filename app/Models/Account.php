<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Account extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'parent_id',
        'type',
        'nature',
        'is_cash_box',
        'supports_multi_currency',
        'default_currency',
        'require_currency_selection',
        'is_group',
        'has_opening_balance',
        'opening_balance',
        'opening_balance_type',
        'opening_balance_date',
        'opening_balance_currency',
        'opening_balance_journal_entry_id',
    ];

    protected $casts = [
        'is_group'    => 'boolean',
        'is_cash_box' => 'boolean',
        'supports_multi_currency' => 'boolean',
    ];
    
    // تمكين التحميل الكسول للعلاقات
    protected $with = ['parent'];

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

    public function balance($currency = null)
    {
        // إذا لم يتم تحديد العملة، استخدم عملة الحساب
        $currency = $currency ?? $this->default_currency;
        
        // تأكد من أن العملة محددة
        if (empty($currency)) {
            return 0;
        }
        
        // مجموع المدين والدائن بعملة محددة
        $debit = $this->journalEntryLines()
            ->where('currency', $currency)
            ->sum('debit');
        $credit = $this->journalEntryLines()
            ->where('currency', $currency)
            ->sum('credit');
        
        // إذا كانت طبيعة الحساب مدين: الرصيد = المدين - الدائن
        // إذا كانت طبيعة الحساب دائن: الرصيد = الدائن - المدين
        if ($this->nature === 'مدين' || $this->nature === 'debit') {
            return $debit - $credit;
        } else {
            return $credit - $debit;
        }
    }

    public function canWithdraw($amount, $currency = null)
    {
        // إذا لم يتم تحديد العملة، استخدم عملة الحساب
        $currency = $currency ?? $this->default_currency;
        return $this->balance($currency) >= $amount;
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'account_user', 'account_id', 'user_id');
    }
}
