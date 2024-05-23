<?php

namespace tests\Application\Service;

use PHPUnit\Framework\TestCase;
use src\Domain\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use src\Application\DTO\MoneyDTO;
use src\Domain\Repository\ExchangeRateRepository;
use src\Domain\Repository\FeePercentageRepository;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeServiceTest
 *
 * Unit tests for CurrencyExchangeService.
 */
class CurrencyExchangeServiceTest extends TestCase
{
    private CurrencyExchangeService $currencyExchangeService;
    private $exchangeRateRepositoryMock;
    private $feePercentageRepositoryMock;

    protected function setUp(): void
    {
        $this->exchangeRateRepositoryMock = $this->createMock(ExchangeRateRepository::class);
        $this->feePercentageRepositoryMock = $this->createMock(FeePercentageRepository::class);
        $this->currencyExchangeService = new CurrencyExchangeService($this->exchangeRateRepositoryMock, $this->feePercentageRepositoryMock);

        $this->feePercentageRepositoryMock->method('getBuyerFeePercentage')->willReturn(99);
        $this->feePercentageRepositoryMock->method('getSellerFeePercentage')->willReturn(1);
    }

    /**
     * @dataProvider conversionDataProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('conversionDataProvider')]
    public function testConvert(string $fromCurrency, string $toCurrency, string $amount, bool $isBuyer, string $expectedResult)
    {
        $fromCurrencyObj = new Currency($fromCurrency);
        $toCurrencyObj = new Currency($toCurrency);
        $amountInSmallestUnit = bcmul($amount, '100', 0);
        $amountMoney = new Money($amountInSmallestUnit, $fromCurrencyObj);
        $currencyExchange = new CurrencyExchange($amountMoney, $fromCurrencyObj, $toCurrencyObj, $isBuyer);

        $dto = new MoneyDTO(new Money(bcmul($expectedResult, '100', 0), $toCurrencyObj), $expectedResult);

        $this->exchangeRateRepositoryMock->method('getExchangeRate')
            ->with($fromCurrencyObj, $toCurrencyObj)
            ->willReturn($this->getMockedExchangeRate($fromCurrency, $toCurrency));

        $result = $this->currencyExchangeService->convert($currencyExchange);

        $this->assertEquals($expectedResult, $result->getFormattedAmount());
    }

    public static function conversionDataProvider(): array
    {
        return [
            'Customer sells 100 EUR for GBP' => ['EUR', 'GBP', '100', false, '158.35'],
            'Customer buys 100 GBP with EUR' => ['GBP', 'EUR', '100', true, '152.78'],
            'Customer sells 100 GBP for EUR' => ['GBP', 'EUR', '100', false, '155.86'],
            'Customer buys 100 EUR with GBP' => ['EUR', 'GBP', '100', true, '155.21'],
            'Customer sells 0.01 EUR for GBP' => ['EUR', 'GBP', '0.01', false, '0.02'], // Small amount
            'Customer buys 0.01 GBP with EUR' => ['GBP', 'EUR', '0.01', true, '0.02'], // Small amount
            'Customer sells 99999999 EUR for GBP' => ['EUR', 'GBP', '99999999', false, '158347798.41'], // Large amount
            'Customer buys 99999999 GBP with EUR' => ['GBP', 'EUR', '99999999', true, '152776798.48'], // Large amount
            'Customer sells 1.123456789 EUR for GBP' => ['EUR', 'GBP', '1.123456789', false, '1.78'], // High precision amount
            'Customer buys 1.987654321 GBP with EUR' => ['GBP', 'EUR', '1.987654321', true, '3.03'], // High precision amount
        ];
    }

    private function getMockedExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $exchangeRates = [
            'EUR' => ['GBP' => 1.5678],
            'GBP' => ['EUR' => 1.5432],
        ];

        return $exchangeRates[$fromCurrency][$toCurrency];
    }
}
