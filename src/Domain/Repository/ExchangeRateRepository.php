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
    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float;
}
