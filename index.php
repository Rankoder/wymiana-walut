<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use src\Application\Controller\CurrencyExchangeController;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Repository\InMemoryFeePercentageRepository;
use src\Domain\Service\CurrencyExchangeService;

// Set up repositories
$exchangeRateRepository = new InMemoryExchangeRateRepository();
$feePercentageRepository = new InMemoryFeePercentageRepository();

// Set up service
$currencyExchangeService = new CurrencyExchangeService($exchangeRateRepository, $feePercentageRepository);

// Set up controller
$currencyExchangeController = new CurrencyExchangeController($currencyExchangeService);

// Test case: Customer sells 100 EUR for GBP
echo "Customer sells 100 EUR for ";
$result = $currencyExchangeController->convert('EUR', 'GBP', '100', false);
echo $result->getCode() . " " . $result->getAmount() . "<br>";

// Test case: Customer buys 100 GBP with EUR
echo "Customer buys 100 GBP with ";
$result = $currencyExchangeController->convert('GBP', 'EUR', '100', true);
echo $result->getCode() . " " . $result->getAmount() . "<br>";

// Test case: Customer sells 100 GBP for EUR
echo "Customer sells 100 GBP for ";
$result = $currencyExchangeController->convert('GBP', 'EUR', '100', false);
echo $result->getCode() . " " . $result->getAmount() . "<br>";

// Test case: Customer buys 100 EUR with GBP
echo "Customer buys 100 EUR with ";
$result = $currencyExchangeController->convert('EUR', 'GBP', '100', true);
echo $result->getCode() . " " . $result->getAmount() . "<br>";
