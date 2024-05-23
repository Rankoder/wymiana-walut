<?php
declare(strict_types=1);

namespace tests\Application\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\Application\Controller\CurrencyExchangeController;
use src\Application\DTO\MoneyDTO;
use src\Domain\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeControllerTest
 *
 * Unit tests for CurrencyExchangeController.
 */
class CurrencyExchangeControllerTest extends TestCase
{
    /**
     * @var CurrencyExchangeService|\PHPUnit\Framework\MockObject\MockObject Mock service for currency exchange
     */
    private CurrencyExchangeService $currencyExchangeServiceMock;

    /**
     * @var CurrencyExchangeController The controller being tested
     */
    private CurrencyExchangeController $controller;

    /**
     * Sets up the test environment.
     */
    protected function setUp(): void
    {
        $this->currencyExchangeServiceMock = $this->createMock(CurrencyExchangeService::class);
        $this->controller = new CurrencyExchangeController($this->currencyExchangeServiceMock);
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
     * @throws \Exception If an error occurs during conversion
     */
    #[DataProvider('convertDataProvider')]
    public function testConvert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer,
                                string $expectedAmount, string $expectedCurrencyCode): void
    {
        $amountInSmallestUnit = bcmul($amount, '100', 0);
        $money = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
        $currencyExchange = new CurrencyExchange($money, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);

        $this->currencyExchangeServiceMock
            ->method('convert')
            ->with($this->equalTo($currencyExchange))
            ->willReturn([$expectedAmount, $expectedCurrencyCode]);

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
    public static function convertDataProvider(): array
    {
        return [
            ['USD', 'EUR', '100.00', true, '85.00', 'EUR'],
            ['EUR', 'USD', '100.00', false, '115.00', 'USD'],
            ['GBP', 'USD', '50.00', true, '70.00', 'USD'],
            ['JPY', 'USD', '1000.00', false, '9.50', 'USD'],
        ];
    }

    /**
     * Tests that the convert method throws an exception.
     */
    public function testConvertThrowsException(): void
    {
        $this->currencyExchangeServiceMock
            ->method('convert')
            ->will($this->throwException(new \Exception('Conversion error')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error: Conversion error');

        $this->controller->convert('USD', 'EUR', '100.00', true);
    }
}
