<?php
namespace App\Models;

use App\Traits\BelongsToTenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryBatch extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'month', 'status', 'created_by', 'approved_by', 'approved_at',
        'tenant_id',
    ];

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
} 