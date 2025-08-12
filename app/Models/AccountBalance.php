<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AccountBalance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'currency_id',
        'balance'];

    /**
     * Get the account that owns this balance.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the currency of this balance.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
