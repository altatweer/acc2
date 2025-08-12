<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'unit_price', 'description'];

    public function invoiceItems()
    {
        return $this->hasMany(\App\Models\InvoiceItem::class, 'item_id');
    }
}
