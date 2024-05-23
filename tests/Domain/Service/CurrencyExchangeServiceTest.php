<?php
declare(strict_types=1);

namespace tests\Domain\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Domain\Entity\CurrencyExchange;
use src\Domain\Repository\ExchangeRateRepository;
use src\Domain\Repository\FeePercentageRepository;
use src\Domain\Service\CurrencyExchangeService;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeServiceTest
 *
 * Unit tests for CurrencyExchangeService.
 */
class CurrencyExchangeServiceTest extends TestCase
{
    /**
     * @var ExchangeRateRepository|\PHPUnit\Framework\MockObject\MockObject Mock repository for exchange rates
     */
    private $exchangeRateRepositoryMock;

    /**
     * @var FeePercentageRepository|\PHPUnit\Framework\MockObject\MockObject Mock repository for fee percentages
     */
    private $feePercentageRepositoryMock;

    /**
     * @var CurrencyExchangeService The service being tested
     */
    private $service;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $this->exchangeRateRepositoryMock = $this->createMock(ExchangeRateRepository::class);
        $this->feePercentageRepositoryMock = $this->createMock(FeePercentageRepository::class);
        $this->service = new CurrencyExchangeService($this->exchangeRateRepositoryMock, $this->feePercentageRepositoryMock);
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
        $currencyExchange = new CurrencyExchange(
            new Money(bcmul($amount, '100', 0), new Currency($fromCurrencyCode)),
            new Currency($fromCurrencyCode),
            new Currency($toCurrencyCode),
            $isBuyer
        );

        $exchangeRates = [
            'EUR' => ['GBP' => 1.5678],
            'GBP' => ['EUR' => 1.5432]
        ];

        $this->exchangeRateRepositoryMock
            ->method('getExchangeRate')
            ->willReturnCallback(function ($fromCurrency, $toCurrency) use ($exchangeRates) {
                return $exchangeRates[$fromCurrency->getCode()][$toCurrency->getCode()];
            });

        $this->feePercentageRepositoryMock
            ->method('getBuyerFeePercentage')
            ->willReturn(99); // 1% buyer fee

        $this->feePercentageRepositoryMock
            ->method('getSellerFeePercentage')
            ->willReturn(1); // 1% seller fee

        [$formattedAmount, $currencyCode] = $this->service->convert($currencyExchange);

        $this->assertEquals($expectedAmount, $formattedAmount);
        $this->assertEquals($expectedCurrencyCode, $currencyCode);
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
     * Tests that the convert method throws an exception.
     */
    public function testConvertThrowsException(): void
    {
        $this->exchangeRateRepositoryMock
            ->method('getExchangeRate')
            ->will($this->throwException(new \Exception('Conversion error')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Conversion error');

        $this->service->convert(new CurrencyExchange(
            new Money(10000, new Currency('USD')),
            new Currency('USD'),
            new Currency('EUR'),
            true
        ));
    }
}
