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
    ];

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
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
}
