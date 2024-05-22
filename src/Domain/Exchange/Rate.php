<?php
declare(strict_types=1);

namespace src\Domain\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use src\Domain\CurrencyCode;
use src\Domain\Money;

/**
 * Rate responsible for currency rate
 */
class Rate
{
    /** @var CurrencyCode */
    private CurrencyCode $fromCurrency;

    /** @var CurrencyCode */
    private CurrencyCode $toCurrency;

    /** @var string */
    private string $rate;

    /**
     * @param CurrencyCode $fromCurrency
     * @param CurrencyCode $toCurrency
     * @param string $rate
     */
    public function __construct(CurrencyCode $fromCurrency, CurrencyCode $toCurrency, string $rate)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->rate = $rate;
    }

    public function convert(Money $money): Money
    {
        if ($money->getCurrencyCode() !== $this->fromCurrency->getCurrencyCode()
            && $this->checkRatioFormat($this->rate)) {
            throw new \InvalidArgumentException('Currency mismatch.');
        }

        return new Money((int)($money->getAmount() * $this->rate), $this->toCurrency);
    }

    public function createPairToExchange() {
        if (!$this->checkRatioFormat($this->rate))
        {
            throw new \InvalidArgumentException('Ratio have wrong format.');
        }

        return new CurrencyPair(
            new Currency($this->fromCurrency->getCurrencyCode()),
            new Currency($this->toCurrency->getCurrencyCode()),
            $this->rate);
    }

    private function checkRatioFormat(string $ratio): bool
    {
        // Wyrażenie regularne do sprawdzenia formatu
        $pattern = '/^\d+\.\d{4}$/';

        // Sprawdzenie, czy string pasuje do wzorca
        return preg_match($pattern, $ratio) === 1;
    }
}
