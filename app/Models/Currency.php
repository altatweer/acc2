<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Currency extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:6',
    ];

    /**
     * Get account balances for this currency
     */
    public function accountBalances()
    {
        return $this->hasMany(AccountBalance::class, 'currency_id');
    }
    
    /**
     * Get the default currency
     * 
     * @return Currency
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first() ?? static::first();
    }
    
    /**
     * Get the default currency code
     * 
     * @return string
     */
    public static function getDefaultCode()
    {
        $default = static::getDefault();
        return $default ? $default->code : 'IQD';
    }
    
    /**
     * Convert amount from this currency to default currency
     * 
     * @param float $amount
     * @return float
     */
    public function convertToDefault($amount)
    {
        return $amount * $this->exchange_rate;
    }
    
    /**
     * Convert amount from default currency to this currency
     * 
     * @param float $amount
     * @return float
     */
    public function convertFromDefault($amount)
    {
        return $amount / $this->exchange_rate;
    }
    
    /**
     * Convert amount from one currency to another
     * 
     * @param float $amount
     * @param string $fromCurrencyCode
     * @param string $toCurrencyCode
     * @return float
     */
    public static function convert($amount, $fromCurrencyCode, $toCurrencyCode)
    {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return $amount;
        }
        
        $fromCurrency = static::where('code', $fromCurrencyCode)->first();
        $toCurrency = static::where('code', $toCurrencyCode)->first();
        
        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }
        
        // First convert to default currency
        $amountInDefault = $fromCurrency->convertToDefault($amount);
        
        // Then convert from default to target currency
        return $toCurrency->convertFromDefault($amountInDefault);
    }
    
    /**
     * Format amount with currency symbol
     * 
     * @param float $amount
     * @param int $decimals
     * @param bool $showSymbol
     * @return string
     */
    public function format($amount, $decimals = 2, $showSymbol = true)
    {
        $formattedAmount = number_format($amount, $decimals);
        
        if (!$showSymbol || empty($this->symbol)) {
            return $formattedAmount;
        }
        
        return $this->symbol . ' ' . $formattedAmount;
    }
    
    /**
     * Format amount with currency symbol by currency code
     * 
     * @param float $amount
     * @param string $currencyCode
     * @param int $decimals
     * @return string
     */
    public static function formatByCurrency($amount, $currencyCode, $decimals = 2)
    {
        $currency = static::where('code', $currencyCode)->first();
        
        if (!$currency) {
            return number_format($amount, $decimals);
        }
        
        return $currency->format($amount, $decimals);
    }
}
