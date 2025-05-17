<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Transaction extends Model
{
   use HasFactory, BelongsToTenant;

   protected $fillable = [
       'voucher_id',
       'date',
       'type',
       'account_id',
       'target_account_id',
       'amount',
       'currency',
       'exchange_rate',
       'description',
       'user_id', // ✅ أضفنا user_id هنا
       'invoice_id',
   ];

   /**
    * العلاقة مع السند (Voucher)
    */
   public function voucher()
   {
       return $this->belongsTo(Voucher::class);
   }

   /**
    * العلاقة مع الحساب المصدر (Account)
    */
   public function account()
   {
       return $this->belongsTo(Account::class, 'account_id');
   }

   /**
    * العلاقة مع الحساب الهدف (Target Account)
    */
   public function targetAccount()
   {
       return $this->belongsTo(Account::class, 'target_account_id');
   }

   // Relationship with the invoice
   public function invoice()
   {
       return $this->belongsTo(Invoice::class, 'invoice_id');
   }
}