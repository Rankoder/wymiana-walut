<?php

require_once 'vendor/autoload.php';
require_once 'src/Test.php';

/**
 * Helper function to test and display the conversion result.
 *
 * @param string $fromCurrency
 * @param string $toCurrency
 * @param string $amount
 * @param string $ratio
 * @param bool $buyer
 */
function testConversion(string $fromCurrency, string $toCurrency, string $amount, string $ratio, bool $isBuyer): void
{
    $test = new \src\Test($fromCurrency, $toCurrency, $amount, $ratio, $isBuyer);
    echo "Converting $amount $fromCurrency to $toCurrency. Buyer: " . ($isBuyer ? 'Yes' : 'No') . "\n";
    echo "Result: " . $test->convert() . " $toCurrency\n\n" . "</br>";
}

// Kursy wymiany walut
$exchangeRates = [
    'EUR_GBP' => '1.5678',
    'GBP_EUR' => '1.5432'
];

// Klient sprzedaje 100 EUR za GBP
testConversion('EUR', 'GBP', '100', $exchangeRates['EUR_GBP'], false);

// Klient kupuje 100 GBP za EUR
testConversion('GBP', 'EUR', '100', $exchangeRates['GBP_EUR'], true);

// Klient sprzedaje 100 GBP za EUR
testConversion('GBP', 'EUR', '100', $exchangeRates['GBP_EUR'], false);

// Klient kupuje 100 EUR za GBP
testConversion('EUR', 'GBP', '100', $exchangeRates['EUR_GBP'], true);
