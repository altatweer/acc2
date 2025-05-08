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
} 