<?php

namespace src\Domain\Entity;

use Money\Money;

/**
 * Class CurrencyExchangeDTO
 *
 * Data Transfer Object for currency exchange operations.
 */
class CurrencyExchangeDTO
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
