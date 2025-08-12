<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'address', 
        'account_id'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(\App\Models\PurchaseInvoice::class, 'supplier_id');
    }
} 