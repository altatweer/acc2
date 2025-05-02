<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Item;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'item_id', 'quantity', 'unit_price', 'line_total'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
