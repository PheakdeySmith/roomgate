<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Format a price in the user's currency
 *
 * @param float $amount The amount in USD
 * @param User|null $user The user whose currency settings to use (defaults to authenticated user)
 * @param bool $includeCode Whether to include the currency code in the output
 * @return string The formatted price
 */
function format_money($amount, $user = null, $includeCode = true) {
    try {
        // Default to authenticated user if not specified
        if (!$user) {
            $user = Auth::user();
        }
        
        // If no user provided or found, return formatted USD
        if (!$user) {
            return '$' . number_format($amount, 2);
        }
        
        // Get user's currency settings with fallback to defaults
        $currencyCode = 'USD';
        $exchangeRate = 1;
        
        try {
            // Try to access the currency fields if they exist
            if (isset($user->currency_code) && !empty($user->currency_code)) {
                $currencyCode = $user->currency_code;
            }
            
            if (isset($user->exchange_rate) && $user->exchange_rate > 0) {
                $exchangeRate = $user->exchange_rate;
            }
        } catch (\Exception $e) {
            // If there's any error, just use the defaults
            Log::info('Currency helper fallback to defaults: ' . $e->getMessage());
        }
        
        return format_money_with_currency($amount, $currencyCode, $exchangeRate, $includeCode);
    } catch (\Exception $e) {
        // In case of any error, return a simple formatted USD amount
        Log::error('Price formatter error: ' . $e->getMessage());
        return '$' . number_format($amount, 2);
    }
}

/**
 * Format a price in a specific currency
 *
 * @param float $amount The amount in USD
 * @param string $currencyCode The currency code
 * @param float $exchangeRate The exchange rate from USD
 * @param bool $includeCode Whether to include the currency code in the output
 * @return string The formatted price
 */
function format_money_with_currency($amount, $currencyCode = 'USD', $exchangeRate = 1, $includeCode = true) {
    try {
        // Ensure amount and exchange rate are numeric
        $amount = is_numeric($amount) ? (float)$amount : 0;
        $exchangeRate = is_numeric($exchangeRate) ? (float)$exchangeRate : 1;
        
        // Convert amount to specified currency
        $convertedAmount = $amount * $exchangeRate;
        
        // Format based on currency
        switch ($currencyCode) {
            case 'USD':
                $formatted = '$' . number_format($convertedAmount, 2);
                break;
            case 'KHR':
                $formatted = number_format($convertedAmount, 0) . '៛';
                break;
            case 'EUR':
                $formatted = '€' . number_format($convertedAmount, 2);
                break;
            case 'GBP':
                $formatted = '£' . number_format($convertedAmount, 2);
                break;
            case 'JPY':
                $formatted = '¥' . number_format($convertedAmount, 0);
                break;
            case 'CNY':
                $formatted = '¥' . number_format($convertedAmount, 2);
                break;
            case 'THB':
                $formatted = '฿' . number_format($convertedAmount, 2);
                break;
            case 'SGD':
                $formatted = 'S$' . number_format($convertedAmount, 2);
                break;
            case 'MYR':
                $formatted = 'RM' . number_format($convertedAmount, 2);
                break;
            case 'VND':
                $formatted = number_format($convertedAmount, 0) . '₫';
                break;
            default:
                $formatted = number_format($convertedAmount, 2);
        }
        
        // Include currency code if requested
        if ($includeCode && $currencyCode !== 'USD') {
            $formatted .= ' ' . $currencyCode;
        }
        
        return $formatted;
    } catch (\Exception $e) {
        // In case of any error, return a simple formatted USD amount
        Log::error('Currency formatter error: ' . $e->getMessage());
        return '$' . number_format($amount, 2);
    }
}
