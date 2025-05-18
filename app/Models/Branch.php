<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Branch extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'tenant_id',
    ];
}
