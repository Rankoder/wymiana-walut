<?php

require_once 'vendor/autoload.php';

use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Repository\InMemoryFeePercentageRepository;
use src\Domain\Service\CurrencyExchangeService;
use src\Application\Controller\CurrencyExchangeController;

// Set up dependencies
$exchangeRateRepository = new InMemoryExchangeRateRepository();
$feePercentageRepository = new InMemoryFeePercentageRepository();
$currencyExchangeService = new CurrencyExchangeService($exchangeRateRepository, $feePercentageRepository);
$currencyExchangeController = new CurrencyExchangeController($currencyExchangeService);

// Test cases
echo "Customer sells 100 EUR for GBP: ";
echo $currencyExchangeController->convert('EUR', 'GBP', '100', false) . "</br>"; // Money amount in smallest unit (cents)

echo "Customer buys 100 GBP with EUR: ";
echo $currencyExchangeController->convert('GBP', 'EUR', '100', true) . "\n"; // Money amount in smallest unit (cents)

echo "Customer sells 100 GBP for EUR: ";
echo $currencyExchangeController->convert('GBP', 'EUR', '100', false) . "\n"; // Money amount in smallest unit (cents)

echo "Customer buys 100 EUR with GBP: ";
echo $currencyExchangeController->convert('EUR', 'GBP', '100', true) . "\n"; // Money amount in smallest unit (cents)
