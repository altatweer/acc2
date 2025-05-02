<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:6',
    ];

    /**
     * Get account balances for this currency
     */
    public function accountBalances()
    {
        return $this->hasMany(AccountBalance::class, 'currency_id');
    }
}
