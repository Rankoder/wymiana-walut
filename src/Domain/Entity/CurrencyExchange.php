<?php

namespace src\Domain\Entity;

use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchange
 *
 * Represents a currency exchange transaction.
 */
class CurrencyExchange
{
    private Money $amount;
    private Currency $fromCurrency;
    private Currency $toCurrency;
    private bool $isBuyer;

    public function __construct(Money $amount, Currency $fromCurrency, Currency $toCurrency, bool $isBuyer)
    {
        $this->amount = $amount;
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->isBuyer = $isBuyer;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getFromCurrency(): Currency
    {
        return $this->fromCurrency;
    }

    public function getToCurrency(): Currency
    {
        return $this->toCurrency;
    }

    public function isBuyer(): bool
    {
        return $this->isBuyer;
    }
}
