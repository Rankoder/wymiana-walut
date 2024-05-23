<?php

namespace tests\Integration;

use PHPUnit\Framework\TestCase;
use src\Application\Controller\CurrencyExchangeController;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Service\CurrencyExchangeService;

/**
 * Class CurrencyExchangeIntegrationTest
 *
 * Integration tests for the currency exchange flow.
 */
class CurrencyExchangeIntegrationTest extends TestCase
{
    private InMemoryExchangeRateRepository $exchangeRateRepository;
    private CurrencyExchangeService $currencyExchangeService;
    private CurrencyExchangeController $currencyExchangeController;

    protected function setUp(): void
    {
        $this->exchangeRateRepository = new InMemoryExchangeRateRepository();
        $this->currencyExchangeService = new CurrencyExchangeService($this->exchangeRateRepository);
        $this->currencyExchangeController = new CurrencyExchangeController($this->currencyExchangeService);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('integrationDataProvider')]
    public function testCurrencyExchangeIntegration(string $fromCurrency, string $toCurrency, string $amount, bool $isBuyer, string $expectedResult)
    {
        $result = $this->currencyExchangeController->convert($fromCurrency, $toCurrency, $amount, $isBuyer);
        $this->assertEquals($expectedResult, $result);
    }

    public static function integrationDataProvider(): array
    {
        return [
            'Client selling 100 EUR for GBP' => ['EUR', 'GBP', '100', false, '158.35'],
            'Client buying 100 GBP for EUR' => ['GBP', 'EUR', '100', true, '152.78'],
            'Client selling 100 GBP for EUR' => ['GBP', 'EUR', '100', false, '155.86'],
            'Client buying 100 EUR for GBP' => ['EUR', 'GBP', '100', true, '155.21'],
        ];
    }

    public function testCurrencyExchangeIntegrationHandlesException()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'USD'; // USD is not defined in the exchange rates
        $amount = '100';
        $isBuyer = true;

        $result = $this->currencyExchangeController->convert($fromCurrency, $toCurrency, $amount, $isBuyer);

        $this->assertStringContainsString('Error: Exchange rate not found', $result);
    }
}
