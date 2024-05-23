<?php

namespace src\Domain\Repository;

use Money\Currency;

/**
 * Interface ExchangeRateRepository
 *
 * Defines the contract for exchange rate repositories.
 */
interface ExchangeRateRepository
{
    /**
     * Gets the exchange rate for a given currency pair.
     *
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @return float
     */
    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float;
}
