<?php

namespace src\Application\DTO;

use Money\Money;

/**
 * Class MoneyDTO
 *
 * Data Transfer Object for money values.
 */
class MoneyDTO
{
    private Money $amount;
    private string $formattedAmount;

    public function __construct(Money $amount, string $formattedAmount)
    {
        $this->amount = $amount;
        $this->formattedAmount = $formattedAmount;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getFormattedAmount(): string
    {
        return $this->formattedAmount;
    }
}
