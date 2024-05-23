<?php
declare(strict_types=1);

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
     * @param Currency $fromCurrency The currency to convert from
     * @param Currency $toCurrency The currency to convert to
     * @return float The exchange rate between the two currencies
     */
    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float;
}
