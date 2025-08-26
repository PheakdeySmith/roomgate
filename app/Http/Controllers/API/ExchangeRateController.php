<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    protected $exchangeRateService;
    
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }
    
    /**
     * Get the exchange rate for a specific currency
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRate(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
        ]);
        
        $currencyCode = strtoupper($request->input('currency'));
        $rate = $this->exchangeRateService->getExchangeRate($currencyCode);
        
        if ($rate === null) {
            return response()->json([
                'success' => false,
                'message' => "Could not fetch exchange rate for $currencyCode"
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'currency' => $currencyCode,
            'rate' => $rate,
            'base' => 'USD'
        ]);
    }
    
    /**
     * Get all supported exchange rates
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRates()
    {
        $rates = $this->exchangeRateService->getAllRates();
        
        if ($rates === null) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch exchange rates'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'base' => 'USD',
            'rates' => $rates
        ]);
    }
    
    /**
     * Get a list of supported currencies
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupportedCurrencies()
    {
        $currencies = $this->exchangeRateService->getSupportedCurrencies();
        
        return response()->json([
            'success' => true,
            'currencies' => $currencies
        ]);
    }
}
