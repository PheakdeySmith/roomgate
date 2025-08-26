<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * The base URL for the ExchangeRate-API
     */
    protected $apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD';
    
    /**
     * The cache time in minutes
     */
    protected $cacheTime = 60; // 1 hour
    
    /**
     * Get the exchange rate for a specific currency
     * 
     * Priority order:
     * 1. For USD, always return 1.0
     * 2. If the user has a stored rate for this currency and we're not ignoring preferences, use that
     * 3. Otherwise, fetch from the API
     *
     * @param string $currencyCode
     * @param bool $ignoreUserPreference - If true, always fetch from API and ignore user's stored preference
     * @return float|null
     */
    public function getExchangeRate(string $currencyCode, bool $ignoreUserPreference = false): ?float
    {
        // For USD, always return 1 as the exchange rate
        if ($currencyCode === 'USD') {
            return 1.0;
        }
        
        try {
            // PRIORITY 1: If we're not ignoring user preferences, check if the current user has a stored rate
            if (!$ignoreUserPreference && auth()->check()) {
                $user = auth()->user();
                if ($user->currency_code === $currencyCode && $user->exchange_rate > 0) {
                    Log::info("Using user's stored exchange rate for $currencyCode: {$user->exchange_rate}");
                    // Return the user's stored exchange rate rounded to 2 decimal places
                    return round((float)$user->exchange_rate, 2);
                }
            }
            
            // PRIORITY 2: If no stored rate or we're ignoring preferences, fetch from API
            $rates = $this->getAllRates();
            
            if (!$rates || !isset($rates[$currencyCode])) {
                Log::warning("Exchange rate not found for currency: $currencyCode");
                return null;
            }
            
            Log::info("Using API exchange rate for $currencyCode: {$rates[$currencyCode]}");
            return round($rates[$currencyCode], 2);
        } catch (\Exception $e) {
            Log::error("Error fetching exchange rate: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all exchange rates
     *
     * @return array|null
     */
    public function getAllRates(): ?array
    {
        try {
            // Try to get from cache first
            if (Cache::has('exchange_rates')) {
                return Cache::get('exchange_rates');
            }
            
            // Fetch from API if not in cache
            $response = Http::get($this->apiUrl);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates'])) {
                    // Cache the rates
                    Cache::put('exchange_rates', $data['rates'], $this->cacheTime);
                    return $data['rates'];
                }
            }
            
            Log::warning("Failed to fetch exchange rates from API. Status: " . $response->status());
            return null;
        } catch (\Exception $e) {
            Log::error("Exception while fetching exchange rates: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get a list of supported currencies with their codes and names
     *
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'KHR' => 'Cambodian Riel',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CNY' => 'Chinese Yuan',
            'THB' => 'Thai Baht',
            'SGD' => 'Singapore Dollar',
            'MYR' => 'Malaysian Ringgit',
            'VND' => 'Vietnamese Dong',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
            'HKD' => 'Hong Kong Dollar',
            'INR' => 'Indian Rupee',
            'IDR' => 'Indonesian Rupiah',
            'KRW' => 'South Korean Won',
            'NZD' => 'New Zealand Dollar',
            'PHP' => 'Philippine Peso',
            'TWD' => 'Taiwan New Dollar'
        ];
    }
}
