<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Helpers/money.php';

// Skip using format_money with a fake user and just use format_money_with_currency directly
$amount = 63.00; // The amount in USD
$exchangeRate = 4018.98;

echo "Original amount: \$" . number_format($amount, 2) . " USD\n";
echo "Calculated amount: " . number_format($amount * $exchangeRate, 0) . "៛ KHR\n";

// Test format_money_with_currency directly
$directFormat = format_money_with_currency($amount, 'KHR', $exchangeRate);
echo "Direct format: " . $directFormat . "\n";
