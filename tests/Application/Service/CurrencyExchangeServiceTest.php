<?php

namespace tests\Application\Service;

use PHPUnit\Framework\TestCase;
use src\Application\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use src\Domain\Repository\ExchangeRateRepository;
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

    protected function setUp(): void
    {
        $this->exchangeRateRepositoryMock = $this->createMock(ExchangeRateRepository::class);
        $this->currencyExchangeService = new CurrencyExchangeService($this->exchangeRateRepositoryMock);
    }


    #[\PHPUnit\Framework\Attributes\DataProvider('conversionDataProvider')]
    public function testConvert(string $fromCurrency, string $toCurrency, string $amount, bool $isBuyer, string $expectedResult)
    {
        $fromCurrencyObj = new Currency($fromCurrency);
        $toCurrencyObj = new Currency($toCurrency);
        $amountInSmallestUnit = bcmul($amount, '100', 0);
        $amountMoney = new Money($amountInSmallestUnit, $fromCurrencyObj);
        $currencyExchange = new CurrencyExchange($amountMoney, $fromCurrencyObj, $toCurrencyObj, $isBuyer);

        $this->exchangeRateRepositoryMock->method('getExchangeRate')
            ->with($fromCurrencyObj, $toCurrencyObj)
            ->willReturn($this->getMockedExchangeRate($fromCurrency, $toCurrency));

        $result = $this->currencyExchangeService->convert($currencyExchange);

        $this->assertEquals($expectedResult, $result);
    }

    public static function conversionDataProvider(): array
    {
        return [
            'Klient sprzedaje 100 EUR za GBP' => ['EUR', 'GBP', '100', false, '158.35'],
            'Klient kupuje 100 GBP za EUR' => ['GBP', 'EUR', '100', true, '152.78'],
            'Klient sprzedaje 100 GBP za EUR' => ['GBP', 'EUR', '100', false, '155.86'],
            'Klient kupuje 100 EUR za GBP' => ['EUR', 'GBP', '100', true, '155.21'],
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
