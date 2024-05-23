<?php

namespace tests\Domain\Repository;

use PHPUnit\Framework\TestCase;
use src\Domain\Repository\ExchangeRateRepository;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use Money\Currency;
use InvalidArgumentException;

/**
 * Class ExchangeRateRepositoryTest
 *
 * Unit tests for ExchangeRateRepository.
 */
class ExchangeRateRepositoryTest extends TestCase
{
    private ExchangeRateRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryExchangeRateRepository();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('exchangeRateProvider')]
    public function testGetExchangeRate(Currency $fromCurrency, Currency $toCurrency, float $expectedRate)
    {
        $rate = $this->repository->getExchangeRate($fromCurrency, $toCurrency);

        $this->assertEquals($expectedRate, $rate);
    }

    public static function exchangeRateProvider(): array
    {
        return [
            'EUR to GBP' => [new Currency('EUR'), new Currency('GBP'), 1.5678],
            'GBP to EUR' => [new Currency('GBP'), new Currency('EUR'), 1.5432],
        ];
    }

    public function testGetExchangeRateWithInvalidCurrency()
    {
        $this->expectException(InvalidArgumentException::class);

        $fromCurrency = new Currency('USD');
        $toCurrency = new Currency('GBP');
        $this->repository->getExchangeRate($fromCurrency, $toCurrency);
    }

    public function testImplementationOfInterface()
    {
        $this->assertInstanceOf(ExchangeRateRepository::class, $this->repository);
    }
}
