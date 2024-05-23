<?php

namespace src\Domain\Entity;

use Money\Currency;
use Money\Money;
use InvalidArgumentException;

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

    /**
     * CurrencyExchange constructor.
     *
     * @param Money $amount
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @param bool $isBuyer
     * @throws InvalidArgumentException
     */
    public function __construct(Money $amount, Currency $fromCurrency, Currency $toCurrency, bool $isBuyer)
    {
        if ($amount->isNegative()) {
            throw new InvalidArgumentException("Amount must be positive.");
        }

        $this->amount = $amount;
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->isBuyer = $isBuyer;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return Currency
     */
    public function getFromCurrency(): Currency
    {
        return $this->fromCurrency;
    }

    /**
     * @return Currency
     */
    public function getToCurrency(): Currency
    {
        return $this->toCurrency;
    }

    /**
     * @return bool
     */
    public function isBuyer(): bool
    {
        return $this->isBuyer;
    }
}
