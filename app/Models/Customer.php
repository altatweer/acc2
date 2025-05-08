<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'account_id'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'customer_id');
    }
}
