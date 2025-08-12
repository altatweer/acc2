<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    /**
     * Get the default currency
     *
     * @return Currency|null
     */
    public static function getDefaultCurrency()
    {
        return Currency::getDefault();
    }

    /**
     * Get the default currency code
     *
     * @return string
     */
    public static function getDefaultCurrencyCode()
    {
        return Currency::getDefaultCode();
    }

    /**
     * Convert amount between currencies
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        return Currency::convert($amount, $fromCurrency, $toCurrency);
    }

    /**
     * Format amount with currency symbol
     *
     * @param float $amount
     * @param string $currencyCode
     * @param int $decimals
     * @return string
     */
    public static function format($amount, $currencyCode = null, $decimals = 2)
    {
        if (empty($currencyCode)) {
            $currencyCode = self::getDefaultCurrencyCode();
        }
        
        return Currency::formatByCurrency($amount, $currencyCode, $decimals);
    }

    /**
     * Convert amount to default currency and format it
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param int $decimals
     * @return string
     */
    public static function convertAndFormat($amount, $fromCurrency, $decimals = 2)
    {
        $defaultCurrency = self::getDefaultCurrencyCode();
        $convertedAmount = self::convert($amount, $fromCurrency, $defaultCurrency);
        
        return self::format($convertedAmount, $defaultCurrency, $decimals);
    }

    /**
     * Convert amount between currencies with historical rates
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string|null $date
     * @return float
     */
    public static function convertWithHistoricalRate($amount, $fromCurrency, $toCurrency, $date = null)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $date = $date ?: now()->format('Y-m-d');
        
        // Try to get historical rate first
        $historicalRate = \DB::table('currency_rates')
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->value('rate');
            
        if ($historicalRate) {
            return $amount * $historicalRate;
        }
        
        // Fallback to current rates
        return self::convert($amount, $fromCurrency, $toCurrency);
    }
    
    /**
     * Convert amount between currencies with high precision
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param int $precision
     * @return float
     */
    public static function convertWithPrecision($amount, $fromCurrency, $toCurrency, $precision = 10)
    {
        if ($fromCurrency === $toCurrency) {
            return round($amount, $precision);
        }
        
        $fromCurrency = Currency::where('code', $fromCurrency)->first();
        $toCurrency = Currency::where('code', $toCurrency)->first();
        
        if (!$fromCurrency || !$toCurrency) {
            return round($amount, $precision);
        }
        
        // Use high precision arithmetic
        $fromRate = (float) $fromCurrency->exchange_rate;
        $toRate = (float) $toCurrency->exchange_rate;
        
        // First convert to default currency (high precision)
        $amountInDefault = bcmul((string) $amount, (string) $fromRate, $precision + 2);
        
        // Then convert from default to target currency (high precision)
        $convertedAmount = bcdiv($amountInDefault, (string) $toRate, $precision + 2);
        
        return (float) round($convertedAmount, $precision);
    }
    
    /**
     * Get exchange rate between two currencies with date
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string|null $date
     * @return array
     */
    public static function getExchangeRateInfo($fromCurrency, $toCurrency, $date = null)
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'rate' => 1.0,
                'date' => $date ?: now()->format('Y-m-d'),
                'source' => 'same_currency',
                'precision' => 6,
            ];
        }
        
        $date = $date ?: now()->format('Y-m-d');
        
        // Try historical rates
        $historicalRate = \DB::table('currency_rates')
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->first();
            
        if ($historicalRate) {
            return [
                'rate' => (float) $historicalRate->rate,
                'date' => $historicalRate->effective_date,
                'source' => 'historical',
                'precision' => 10,
            ];
        }
        
        // Fallback to current rates
        $fromCurrencyModel = Currency::where('code', $fromCurrency)->first();
        $toCurrencyModel = Currency::where('code', $toCurrency)->first();
        
        if (!$fromCurrencyModel || !$toCurrencyModel) {
            return [
                'rate' => 1.0,
                'date' => $date,
                'source' => 'fallback',
                'precision' => 6,
            ];
        }
        
        // Calculate cross rate through default currency
        $rate = $fromCurrencyModel->exchange_rate / $toCurrencyModel->exchange_rate;
        
        return [
            'rate' => (float) $rate,
            'date' => $fromCurrencyModel->updated_at->format('Y-m-d'),
            'source' => 'current',
            'precision' => 8,
        ];
    }
} 