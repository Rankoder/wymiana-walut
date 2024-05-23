<?php

namespace tests\Integration;

use PHPUnit\Framework\TestCase;
use src\Domain\Service\CurrencyExchangeService;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Repository\InMemoryFeePercentageRepository;
use src\Application\Controller\CurrencyExchangeController;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeIntegrationTest
 *
 * Integration tests for the currency exchange flow.
 */
class CurrencyExchangeIntegrationTest extends TestCase
{
    private InMemoryExchangeRateRepository $exchangeRateRepository;
    private InMemoryFeePercentageRepository $feePercentageRepository;
    private CurrencyExchangeService $currencyExchangeService;
    private CurrencyExchangeController $currencyExchangeController;

    protected function setUp(): void
    {
        $this->exchangeRateRepository = new InMemoryExchangeRateRepository();
        $this->feePercentageRepository = new InMemoryFeePercentageRepository();
        $this->currencyExchangeService = new CurrencyExchangeService($this->exchangeRateRepository, $this->feePercentageRepository);
        $this->currencyExchangeController = new CurrencyExchangeController($this->currencyExchangeService);
    }

    /**
     * @dataProvider integrationDataProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('integrationDataProvider')]
    public function testCurrencyExchangeIntegration(string $fromCurrency, string $toCurrency, string $amount, bool $isBuyer, string $expectedResult)
    {
        $result = $this->currencyExchangeController->convert($fromCurrency, $toCurrency, $amount, $isBuyer);
        $this->assertEquals($expectedResult, $result);
    }

    public static function integrationDataProvider(): array
    {
        return [
            'Customer sells 100 EUR for GBP' => ['EUR', 'GBP', '100', false, '158.35'],
            'Customer buys 100 GBP with EUR' => ['GBP', 'EUR', '100', true, '152.78'],
            'Customer sells 100 GBP for EUR' => ['GBP', 'EUR', '100', false, '155.86'],
            'Customer buys 100 EUR with GBP' => ['EUR', 'GBP', '100', true, '155.21'],
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
