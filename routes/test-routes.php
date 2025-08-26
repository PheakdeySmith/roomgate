<?php

Route::get('/test-currency', function () {
    $user = auth()->user();
    $amount = 63.00; // The test amount
    
    $formattedWithCurrency = format_money_with_currency($amount, 'KHR', 4018.98);
    $formattedForUser = format_money($amount);
    
    return response()->json([
        'user_currency_code' => $user ? $user->currency_code : 'Not logged in',
        'user_exchange_rate' => $user ? $user->exchange_rate : 'Not logged in',
        'original_amount' => $amount,
        'formatted_with_currency' => $formattedWithCurrency,
        'formatted_for_user' => $formattedForUser,
    ]);
});
