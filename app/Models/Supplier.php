<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Traits\BelongsToTenant;

class Supplier extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'address', 
        'account_id',
        'tenant_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(\App\Models\PurchaseInvoice::class, 'supplier_id');
    }
} 