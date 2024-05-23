<?php

require_once 'vendor/autoload.php';

use src\Application\Controller\CurrencyExchangeController;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Service\CurrencyExchangeService;

// Set up dependencies
$exchangeRateRepository = new InMemoryExchangeRateRepository();
$currencyExchangeService = new CurrencyExchangeService($exchangeRateRepository);
$currencyExchangeController = new CurrencyExchangeController($currencyExchangeService);

// Test cases
echo "Klient sprzedaje 100 EUR za GBP: ";
echo $currencyExchangeController->convert('EUR', 'GBP', '100', false) . "</br>"; // Money amount in smallest unit (cents)

echo "Klient kupuje 100 GBP za EUR: ";
echo $currencyExchangeController->convert('GBP', 'EUR', '100', true) . "</br>"; // Money amount in smallest unit (cents)

echo "Klient sprzedaje 100 GBP za EUR: ";
echo $currencyExchangeController->convert('GBP', 'EUR', '100', false) . "</br>"; // Money amount in smallest unit (cents)

echo "Klient kupuje 100 EUR za GBP: ";
echo $currencyExchangeController->convert('EUR', 'GBP', '100', true) . "</br>"; // Money amount in smallest unit (cents)
