<?php

namespace tests\Application\Controller;

use PHPUnit\Framework\TestCase;
use src\Domain\Service\CurrencyExchangeService;
use src\Application\Controller\CurrencyExchangeController;
use src\Domain\Entity\CurrencyExchange;
use src\Domain\Entity\CurrencyExchangeDTO;
use Money\Currency;
use Money\Money;
use Exception;

/**
 * Class CurrencyExchangeControllerTest
 *
 * Unit tests for CurrencyExchangeController.
 */
class CurrencyExchangeControllerTest extends TestCase
{
    private $currencyExchangeServiceMock;
    private CurrencyExchangeController $currencyExchangeController;

    protected function setUp(): void
    {
        $this->currencyExchangeServiceMock = $this->createMock(CurrencyExchangeService::class);
        $this->currencyExchangeController = new CurrencyExchangeController($this->currencyExchangeServiceMock);
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

        $dto = new CurrencyExchangeDTO(new Money(bcmul($expectedResult, '100', 0), $toCurrencyObj), $expectedResult);

        $this->currencyExchangeServiceMock->expects($this->once())
            ->method('convert')
            ->with($currencyExchange)
            ->willReturn($dto);

        $result = $this->currencyExchangeController->convert($fromCurrency, $toCurrency, $amount, $isBuyer);

        $this->assertEquals($expectedResult, $result);
    }

    public static function conversionDataProvider(): array
    {
        return [
            'Customer sells 100 EUR for GBP' => ['EUR', 'GBP', '100', false, '158.35'],
            'Customer buys 100 GBP with EUR' => ['GBP', 'EUR', '100', true, '152.78'],
            'Customer sells 100 GBP for EUR' => ['GBP', 'EUR', '100', false, '155.86'],
            'Customer buys 100 EUR with GBP' => ['EUR', 'GBP', '100', true, '155.21'],
        ];
    }

    public function testConvertHandlesException()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';
        $amount = '100';
        $isBuyer = true;

        $this->currencyExchangeServiceMock->method('convert')
            ->will($this->throwException(new Exception('Conversion error')));

        $result = $this->currencyExchangeController->convert($fromCurrency, $toCurrency, $amount, $isBuyer);

        $this->assertStringContainsString('Error: Conversion error', $result);
    }
}
