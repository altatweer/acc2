<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'type', 'unit_price', 'description'];

    public function invoiceItems()
    {
        return $this->hasMany(\App\Models\InvoiceItem::class, 'item_id');
    }
}
