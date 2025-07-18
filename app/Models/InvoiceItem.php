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
        'currency',
        'exchange_rate',
        'base_currency_total',
        'tenant_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2', 
        'exchange_rate' => 'decimal:10',
        'base_currency_total' => 'decimal:4',
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
