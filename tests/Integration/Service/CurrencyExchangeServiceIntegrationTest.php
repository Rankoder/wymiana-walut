<?php
declare(strict_types=1);

namespace tests\Integration\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Application\Controller\CurrencyExchangeController;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use src\Domain\Repository\InMemoryFeePercentageRepository;
use src\Domain\Service\CurrencyExchangeService;
use src\Application\DTO\MoneyDTO;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeServiceIntegrationTest
 *
 * Integration tests for the currency exchange process.
 */
class CurrencyExchangeServiceIntegrationTest extends TestCase
{
    /**
     * @var CurrencyExchangeController The controller being tested
     */
    private CurrencyExchangeController $controller;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $exchangeRateRepository = new InMemoryExchangeRateRepository();
        $feePercentageRepository = new InMemoryFeePercentageRepository();
        $currencyExchangeService = new CurrencyExchangeService($exchangeRateRepository, $feePercentageRepository);
        $this->controller = new CurrencyExchangeController($currencyExchangeService);
    }

    /**
     * Tests the convert method.
     *
     * @param string $fromCurrencyCode The source currency code
     * @param string $toCurrencyCode The target currency code
     * @param string $amount The amount to convert
     * @param bool $isBuyer Whether the conversion is for a buyer
     * @param string $expectedAmount The expected converted amount
     * @param string $expectedCurrencyCode The expected currency code
     *
     */
    #[DataProvider('currencyExchangeProvider')]
    public function testConvert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer,
                                string $expectedAmount, string $expectedCurrencyCode): void
    {
        $result = $this->controller->convert($fromCurrencyCode, $toCurrencyCode, $amount, $isBuyer);

        $this->assertInstanceOf(MoneyDTO::class, $result);
        $this->assertEquals($expectedAmount, $result->getAmount());
        $this->assertEquals($expectedCurrencyCode, $result->getCode());
    }

    /**
     * Provides data for the testConvert method.
     *
     * @return array The data sets for the testConvert method
     */
    public static function currencyExchangeProvider(): array
    {
        return [
            'Customer sells 100 EUR for GBP' => ['EUR', 'GBP', '100', false, '158.35', 'GBP'],
            'Customer buys 100 GBP with EUR' => ['GBP', 'EUR', '100', true, '152.78', 'EUR'],
            'Customer sells 100 GBP for EUR' => ['GBP', 'EUR', '100', false, '155.86', 'EUR'],
            'Customer buys 100 EUR with GBP' => ['EUR', 'GBP', '100', true, '155.21', 'GBP'],
            'Customer sells 0.01 EUR for GBP' => ['EUR', 'GBP', '0.01', false, '0.02', 'GBP'], // Small amount
            'Customer buys 0.01 GBP with EUR' => ['GBP', 'EUR', '0.01', true, '0.02', 'EUR'], // Small amount
            'Customer sells 99999999 EUR for GBP' => ['EUR', 'GBP', '99999999', false, '158347798.41', 'GBP'], // Large amount
            'Customer buys 99999999 GBP with EUR' => ['GBP', 'EUR', '99999999', true, '152776798.48', 'EUR'], // Large amount
            'Customer sells 1.123456789 EUR for GBP' => ['EUR', 'GBP', '1.123456789', false, '1.78', 'GBP'], // High precision amount
            'Customer buys 1.987654321 GBP with EUR' => ['GBP', 'EUR', '1.987654321', true, '3.03', 'EUR'], // High precision amount
        ];
    }

    /**
     * Tests that the convert method handles exceptions.
     */
    public function testConvertThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error: Exchange rate not found for EUR to USD.');

        $this->controller->convert('EUR', 'USD', '100.00', true);
    }
}
