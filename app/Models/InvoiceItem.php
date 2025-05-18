<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Item;

class InvoiceItem extends Model
{
    use HasFactory, BelongsToTenant;
    
    protected $fillable = [
        'invoice_id', 
        'item_id', 
        'quantity', 
        'unit_price', 
        'line_total',
        'tenant_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
