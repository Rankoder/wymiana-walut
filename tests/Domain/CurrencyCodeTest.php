<?php
declare(strict_types=1);

namespace Tests\Domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Domain\CurrencyCode;

/**
 * Test currency class
 */
class CurrencyCodeTest extends TestCase
{
    /**
     * Test if method return valid value
     * @param string $currencyCode
     * @return void
     * @throws \Exception
     */
    #[DataProvider('validCurrencyProvider')]
    public function testValidCurrency(string $currencyCode): void
    {
        $currency = new CurrencyCode($currencyCode);
        $this->assertEquals($currencyCode, $currency->getCurrencyCode());
    }

    /**
     * Data Provider with correct Currency
     * @return array[]
     */
    static public function validCurrencyProvider(): array
    {
        return [
            ['EUR'],
            ['GBP'],
        ];
    }

    /**
     * This test check if method can catch incorrect currency
     * @param string $currencyCode
     * @return void
     * @throws \Exception
     */
    #[DataProvider('invalidCurrencyProvider')]
    public function testInvalidCurrency(string $currencyCode): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong currency code');
        new CurrencyCode($currencyCode);
    }

    /**
     * Data Provider with wrong currency
     * @return array[]
     */
    static public function invalidCurrencyProvider(): array
    {
        return [
            ['USD'],
            ['JPY'],
            ['CHF'],
            ['PLN'],
            ['test'],
            [''],
        ];
    }
}
