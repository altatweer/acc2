<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'date',
        'total',
        'currency',
        'exchange_rate',
        'status',
        'created_by'];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'exchange_rate' => 'decimal:6'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Relationship with vouchers (payments)
    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'invoice_id');
    }

    // Relationship with transactions (journal entries)
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }
}
