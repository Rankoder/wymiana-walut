<?php
declare(strict_types=1);

namespace src\Domain\Entity;

use InvalidArgumentException;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchange
 *
 * Represents a currency exchange transaction.
 */
class CurrencyExchange
{
    /**
     * @var Money The amount of money to be exchanged
     */
    private Money $amount;

    /**
     * @var Currency The currency to convert from
     */
    private Currency $fromCurrency;

    /**
     * @var Currency The currency to convert to
     */
    private Currency $toCurrency;

    /**
     * @var bool Indicates if the transaction is for a buyer
     */
    private bool $isBuyer;

    /**
     * CurrencyExchange constructor.
     *
     * @param Money $amount The amount of money to be exchanged
     * @param Currency $fromCurrency The currency to convert from
     * @param Currency $toCurrency The currency to convert to
     * @param bool $isBuyer Indicates if the transaction is for a buyer
     *
     * @throws InvalidArgumentException If the amount is negative
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
     * Gets the amount of money to be exchanged.
     *
     * @return Money The amount of money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * Gets the currency to convert from.
     *
     * @return Currency The source currency
     */
    public function getFromCurrency(): Currency
    {
        return $this->fromCurrency;
    }

    /**
     * Gets the currency to convert to.
     *
     * @return Currency The target currency
     */
    public function getToCurrency(): Currency
    {
        return $this->toCurrency;
    }

    /**
     * Indicates if the transaction is for a buyer.
     *
     * @return bool True if the transaction is for a buyer, false otherwise
     */
    public function isBuyer(): bool
    {
        return $this->isBuyer;
    }
}
