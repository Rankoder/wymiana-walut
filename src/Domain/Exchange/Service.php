<?php

namespace src\Domain\Exchange;

use src\Domain\Money;

class Service
{
    private Rate $exchangeRate;
    private FeePolicy $feePolicy;

    public function __construct(Rate $exchangeRate, FeePolicy $feePolicy)
    {
        $this->exchangeRate = $exchangeRate;
        $this->feePolicy = $feePolicy;
    }

    public function exchange(Money $money, bool $isBuying): Money
    {
        $convertedMoney = $this->exchangeRate->convert($money);

        if ($isBuying) {
            $fee = $this->feePolicy->applyFee($money);
        } else {
            $fee = $this->feePolicy->applyFee($convertedMoney);
        }

        return $convertedMoney->subtract($fee);
    }
}
