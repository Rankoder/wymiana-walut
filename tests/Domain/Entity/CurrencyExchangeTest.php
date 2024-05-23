<?php
declare(strict_types=1);

namespace tests\Domain\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Domain\Entity\CurrencyExchange;
use Money\Currency;
use Money\Money;
use InvalidArgumentException;

/**
 * Class CurrencyExchangeTest
 *
 * Unit tests for CurrencyExchange entity.
 */
class CurrencyExchangeTest extends TestCase
{
    /**
     * Tests the creation of a CurrencyExchange entity.
     *
     * @param Money $amount The amount of money to be exchanged
     * @param Currency $fromCurrency The currency to convert from
     * @param Currency $toCurrency The currency to convert to
     * @param bool $isBuyer Indicates if the transaction is for a buyer
     *
     */
    #[DataProvider('currencyExchangeProvider')]
    public function testCurrencyExchangeCreation(Money $amount, Currency $fromCurrency, Currency $toCurrency, bool $isBuyer): void
    {
        $currencyExchange = new CurrencyExchange($amount, $fromCurrency, $toCurrency, $isBuyer);

        $this->assertInstanceOf(CurrencyExchange::class, $currencyExchange);
        $this->assertEquals($amount, $currencyExchange->getAmount());
        $this->assertEquals($fromCurrency, $currencyExchange->getFromCurrency());
        $this->assertEquals($toCurrency, $currencyExchange->getToCurrency());
        $this->assertEquals($isBuyer, $currencyExchange->isBuyer());
    }

    /**
     * Provides data for the testCurrencyExchangeCreation method.
     *
     * @return array The data sets for the testCurrencyExchangeCreation method
     */
    public static function currencyExchangeProvider(): array
    {
        return [
            'Positive amount, buyer' => [
                new Money(10000, new Currency('EUR')), new Currency('EUR'), new Currency('GBP'), true
            ],
            'Positive amount, seller' => [
                new Money(10000, new Currency('GBP')), new Currency('GBP'), new Currency('EUR'), false
            ],
        ];
    }

    /**
     * Tests the creation of a CurrencyExchange entity with a negative amount.
     */
    public function testCurrencyExchangeWithNegativeAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $amount = new Money(-10000, new Currency('EUR'));
        $fromCurrency = new Currency('EUR');
        $toCurrency = new Currency('GBP');
        $isBuyer = true;

        new CurrencyExchange($amount, $fromCurrency, $toCurrency, $isBuyer);
    }
}
