<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    use HasFactory, BelongsToTenant;
    
    protected $fillable = [
        'key',
        'value',
        'currency',
        'tenant_id',
    ];

    public static function get($key, $currency = null)
    {
        $query = static::where('key', $key);
        if ($currency) {
            $query->where('currency', $currency);
        }
        $row = $query->first();
        return $row ? $row->value : null;
    }

    public static function set($key, $value, $currency = null)
    {
        return static::updateOrCreate([
            'key' => $key,
            'currency' => $currency,
        ], [
            'value' => $value,
        ]);
    }
} 