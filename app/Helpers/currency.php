<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Formats a price based on a user's currency preference.
 *
 * This function acts as a wrapper around format_currency(), automatically
 * resolving the currency code and exchange rate from a user object.
 *
 * @param float $amount The base amount in USD.
 * @param User|null $user The user object. Defaults to the authenticated user.
 * @param bool $includeCode Toggles the inclusion of the currency code in the output string.
 * @return string The formatted price string.
 */
function format_price(float $amount, ?User $user = null, bool $includeCode = true): string
{
    // If no user is provided, default to the currently authenticated user.
    $user = $user ?? Auth::user();

    // Use null-safe and null coalescing operators to safely get user preferences with fallbacks.
    $currencyCode = $user?->currency_code ?? 'USD';
    $exchangeRate = ($user?->exchange_rate > 0) ? $user->exchange_rate : 1.0;

    // Delegate the formatting to the main format_currency function.
    return format_currency($amount, $currencyCode, $exchangeRate, $includeCode);
}

/**
 * Formats and converts an amount for a specific currency.
 *
 * This is the primary formatting engine. It uses a centralized configuration
 * array, making it easy to add or modify currency formats without changing the code logic.
 *
 * @param float $amount The base amount in USD.
 * @param string $currencyCode The target ISO currency code (e.g., 'EUR', 'JPY').
 * @param float $exchangeRate The exchange rate to convert from USD to the target currency.
 * @param bool $includeCode Toggles the inclusion of the currency code in the output string.
 * @return string The formatted price string.
 */
function format_currency(
    float $amount,
    string $currencyCode = 'USD',
    float $exchangeRate = 1.0,
    bool $includeCode = true
): string {
    // A static array stores currency formats efficiently.
    // It's initialized only once per request.
    static $currencies = [
        // code => [symbol, decimals, is_symbol_first, dec_point, thousands_sep]
        'USD' => ['$', 2, true, '.', ','],
        'KHR' => ['៛', 0, false, '.', ','],
        'EUR' => ['€', 2, true, ',', '.'],
        'GBP' => ['£', 2, true, '.', ','],
        'JPY' => ['¥', 0, true, '.', ','],
        'CNY' => ['¥', 2, true, '.', ','],
        'THB' => ['฿', 2, true, '.', ','],
        'SGD' => ['S$', 2, true, '.', ','],
        'MYR' => ['RM', 2, true, '.', ','],
        'VND' => ['₫', 0, false, '.', ','],
    ];

    // Use the specified currency config or a safe default for unknown codes.
    [$symbol, $decimals, $symbolFirst, $decPoint, $thousandsSep] =
        $currencies[strtoupper($currencyCode)] ?? ['', 2, true, '.', ','];

    $convertedAmount = $amount * $exchangeRate;

    $formattedNumber = number_format($convertedAmount, $decimals, $decPoint, $thousandsSep);

    // Assemble the final string based on whether the symbol comes first.
    $formatted = $symbolFirst
        ? $symbol . $formattedNumber
        : $formattedNumber . $symbol;

    // Append the currency code if requested (and not USD).
    if ($includeCode && strtoupper($currencyCode) !== 'USD') {
        $formatted .= ' ' . $currencyCode;
    }

    return $formatted;
}