<?php

namespace Tests\Domain\Exchange;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use src\Domain\CurrencyCode;
use src\Domain\Money;
use src\Domain\Exchange\FeePolicy;

class FeePolicyTest extends TestCase
{
    private FeePolicy $exchangeFeePolicy;

    protected function setUp(): void
    {
        $this->exchangeFeePolicy = new FeePolicy(0.01); // Domyślny wskaźnik opłat 1%
    }

    /**
     * @throws Exception
     */
    #[DataProvider('moneyProvider')]
    public function testApplyFeeWithMock(int $amount, string $currencyCode, float $feeRate, int $expectedFee): void
    {
        // Mock dla klasy Currency
        $currencyMock = $this->createMock(CurrencyCode::class);
        $currencyMock->method('getCurrencyCode')->willReturn($currencyCode);

        // Użycie mocka Currency w obiekcie Money
        $money = new Money($amount, $currencyMock);

        // Aktualizacja wskaźnika opłat
        $this->exchangeFeePolicy = new FeePolicy($feeRate);

        $feeMoney = $this->exchangeFeePolicy->applyFee($money);

        $this->assertEquals($expectedFee, $feeMoney->getAmount());
        $this->assertEquals($currencyCode, $feeMoney->getCurrencyCode());
    }

    public static function moneyProvider(): array
    {
        return [
            [1000, 'EUR', 0.01, 10],
            [2000, 'GBP', 0.01, 20],
            [1500, 'EUR', 0.02, 30],
            [500, 'GBP', 0.03, 15],
        ];
    }
}
