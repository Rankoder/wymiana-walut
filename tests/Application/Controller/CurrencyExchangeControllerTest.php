<?php
declare(strict_types=1);

namespace tests\Application\Controller;

use PHPUnit\Framework\TestCase;
use src\Application\Controller\CurrencyExchangeController;
use src\Application\DTO\MoneyDTO;
use src\Domain\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use Money\Currency;
use Money\Money;

class CurrencyExchangeControllerTest extends TestCase
{
    private CurrencyExchangeService $currencyExchangeServiceMock;
    private CurrencyExchangeController $controller;

    protected function setUp(): void
    {
        $this->currencyExchangeServiceMock = $this->createMock(CurrencyExchangeService::class);
        $this->controller = new CurrencyExchangeController($this->currencyExchangeServiceMock);
    }

    /**
     * @throws \Exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('convertDataProvider')]
    public function testConvert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer, string $expectedAmount, string $expectedCurrencyCode): void
    {
        $amountInSmallestUnit = bcmul($amount, '100', 0);
        $money = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
        $currencyExchange = new CurrencyExchange($money, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);

        $this->currencyExchangeServiceMock
            ->method('convert')
            ->with($currencyExchange)
            ->willReturn([$expectedAmount, $expectedCurrencyCode]);

        $result = $this->controller->convert($fromCurrencyCode, $toCurrencyCode, $amount, $isBuyer);

        $this->assertInstanceOf(MoneyDTO::class, $result);
        $this->assertEquals($expectedAmount, $result->getAmount());
        $this->assertEquals($expectedCurrencyCode, $result->getCode());
    }

    public static function convertDataProvider(): array
    {
        return [
            ['USD', 'EUR', '100.00', true, '85.00', 'EUR'],
            ['EUR', 'USD', '100.00', false, '115.00', 'USD'],
            ['GBP', 'USD', '50.00', true, '70.00', 'USD'],
            ['JPY', 'USD', '1000.00', false, '9.50', 'USD'],
        ];
    }

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
