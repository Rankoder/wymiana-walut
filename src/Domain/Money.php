<?php
declare(strict_types=1);

namespace src\Domain;

/**
 * Class representing money in a given currency.
 * @method getCurrency()
 */
class Money implements MoneyInterface
{
    /** @var int $amount */
    private int $amount;
    private CurrencyCode $currency;

    /**
     * @param int $amount
     * @param CurrencyCode $currency
     */
    public function __construct(int $amount, CurrencyCode $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currency->getCurrencyCode();
    }

    /**
     * @param MoneyInterface $fee
     * @return MoneyInterface
     */
    public function addFee(MoneyInterface $fee): MoneyInterface
    {
        if ($this->currency->getCurrencyCode() !== $fee->getCurrencyCode()) {
            throw new \InvalidArgumentException('Currencies must match to add money');
        }

        return new Money($this->amount + $fee->getAmount(), $this->currency);
    }

    /**
     * @param Money|MoneyInterface $fee
     * @return Money
     */
    public function subtractFee(Money|MoneyInterface $fee): Money
    {
        if ($this->currency->getCurrencyCode() !== $fee->getCurrencyCode()) {
            throw new \InvalidArgumentException('Currencies must match to subtract money.');
        }

        return new Money($this->amount - $fee->getAmount(), $this->currency);
    }

    /**
     * @param float $exchangeRate
     * @return Money
     */
    public function convertCurrency(float $exchangeRate): Money
    {
        return new Money((int)($this->amount * $exchangeRate), $this->currency);
    }
}
