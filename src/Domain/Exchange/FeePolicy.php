<?php
declare(strict_types=1);

namespace src\Domain\Exchange;

use src\Domain\CurrencyCode;
use src\Domain\Money;

class FeePolicy
{
    private float $feeRate;

    public function __construct(float $feeRate)
    {
        $this->feeRate = $feeRate;
    }

    /**
     * @throws \Exception
     */
    public function applyFee(Money $money): Money
    {
        $fee = round($money->getAmount() * $this->feeRate, 2);
        return new Money($fee, (new CurrencyCode($money->getCurrencyCode())));
    }
}