<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class JournalEntry extends Model
{
    use HasFactory;
    
    protected $casts = [
        'is_multi_currency' => 'boolean',
        'exchange_rate' => 'float',
        'total_debit' => 'float',
        'total_credit' => 'float'];

    protected $fillable = [
        'date',
        'description',
        'source_type',
        'source_id',
        'created_by',
        'currency',
        'exchange_rate',
        'is_multi_currency',
        'total_debit',
        'total_credit',
        'status'];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Polymorphic source (invoice, voucher, manual)
    public function source()
    {
        return $this->morphTo(null, 'source_type', 'source_id');
    }
}
