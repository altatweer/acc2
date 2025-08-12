<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_number',
        'type',
        'date',
        'description',
        'created_by',
        'recipient_name',
        'currency',
        'exchange_rate',
        'invoice_id',
        'status'];

    protected $casts = [
        'date' => 'date'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function journalEntry()
    {
        return $this->morphOne(\App\Models\JournalEntry::class, 'source');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
