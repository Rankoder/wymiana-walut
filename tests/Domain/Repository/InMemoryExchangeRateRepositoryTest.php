<?php
declare(strict_types=1);

namespace tests\Domain\Repository;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Domain\Repository\InMemoryExchangeRateRepository;
use Money\Currency;
use InvalidArgumentException;

/**
 * Class InMemoryExchangeRateRepositoryTest
 *
 * Unit tests for InMemoryExchangeRateRepository.
 */
class InMemoryExchangeRateRepositoryTest extends TestCase
{
    /**
     * @var InMemoryExchangeRateRepository The repository being tested
     */
    private InMemoryExchangeRateRepository $repository;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $this->repository = new InMemoryExchangeRateRepository();
    }

    /**
     * Tests the getExchangeRate method.
     *
     * @param Currency $fromCurrency The source currency
     * @param Currency $toCurrency The target currency
     * @param float $expectedRate The expected exchange rate
     *
     */
    #[DataProvider('exchangeRateProvider')]
    public function testGetExchangeRate(Currency $fromCurrency, Currency $toCurrency, float $expectedRate): void
    {
        $rate = $this->repository->getExchangeRate($fromCurrency, $toCurrency);

        $this->assertEquals($expectedRate, $rate);
    }

    /**
     * Provides data for the testGetExchangeRate method.
     *
     * @return array The data sets for the testGetExchangeRate method
     */
    public static function exchangeRateProvider(): array
    {
        return [
            'EUR to GBP' => [new Currency('EUR'), new Currency('GBP'), 1.5678],
            'GBP to EUR' => [new Currency('GBP'), new Currency('EUR'), 1.5432],
        ];
    }

    /**
     * Tests the getExchangeRate method with an invalid currency pair.
     */
    public function testGetExchangeRateWithInvalidCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $fromCurrency = new Currency('USD');
        $toCurrency = new Currency('GBP');
        $this->repository->getExchangeRate($fromCurrency, $toCurrency);
    }
}
