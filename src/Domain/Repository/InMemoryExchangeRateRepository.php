<?php

namespace src\Domain\Repository;

use Money\Currency;

/**
 * Class InMemoryExchangeRateRepository
 *
 * In-memory implementation of ExchangeRateRepository for demonstration purposes.
 */
class InMemoryExchangeRateRepository implements ExchangeRateRepository
{
    private array $exchangeRates;

    public function __construct()
    {
        $this->exchangeRates = [
            'EUR' => ['GBP' => 1],
            'GBP' => ['EUR' => 1.5432],
        ];
    }

    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float
    {
        return $this->exchangeRates[$fromCurrency->getCode()][$toCurrency->getCode()];
    }
}
