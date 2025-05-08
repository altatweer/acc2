<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('default_currency')) {
    /**
     * Get the default currency
     *
     * @return \App\Models\Currency|null
     */
    function default_currency()
    {
        return CurrencyHelper::getDefaultCurrency();
    }
}

if (!function_exists('default_currency_code')) {
    /**
     * Get the default currency code
     *
     * @return string
     */
    function default_currency_code()
    {
        return CurrencyHelper::getDefaultCurrencyCode();
    }
}

if (!function_exists('currency_convert')) {
    /**
     * Convert amount between currencies
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    function currency_convert($amount, $fromCurrency, $toCurrency = null)
    {
        if ($toCurrency === null) {
            $toCurrency = default_currency_code();
        }
        
        return CurrencyHelper::convert($amount, $fromCurrency, $toCurrency);
    }
}

if (!function_exists('currency_format')) {
    /**
     * Format amount with currency symbol
     *
     * @param float $amount
     * @param string|null $currencyCode
     * @param int $decimals
     * @return string
     */
    function currency_format($amount, $currencyCode = null, $decimals = 2)
    {
        return CurrencyHelper::format($amount, $currencyCode, $decimals);
    }
}

if (!function_exists('money')) {
    /**
     * Convert amount to default currency and format it
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param int $decimals
     * @return string
     */
    function money($amount, $fromCurrency = null, $decimals = 2)
    {
        if ($fromCurrency === null) {
            $fromCurrency = default_currency_code();
        }
        
        return CurrencyHelper::convertAndFormat($amount, $fromCurrency, $decimals);
    }
} 