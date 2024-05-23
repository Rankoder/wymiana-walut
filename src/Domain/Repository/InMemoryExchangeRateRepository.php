<?php
declare(strict_types=1);

namespace src\Domain\Repository;

use Money\Currency;
use InvalidArgumentException;

/**
 * Class InMemoryExchangeRateRepository
 *
 * In-memory implementation of ExchangeRateRepository for demonstration purposes.
 */
class InMemoryExchangeRateRepository implements ExchangeRateRepository
{
    /**
     * @var array An associative array of exchange rates
     */
    private array $exchangeRates;

    /**
     * InMemoryExchangeRateRepository constructor.
     *
     * Loads exchange rates from configuration.
     */
    public function __construct()
    {
        $config = require __DIR__ . '/../../Config/config.php';
        $this->exchangeRates = $config['exchange_rates'];
    }

    /**
     * Gets the exchange rate for a given currency pair.
     *
     * @param Currency $fromCurrency The currency to convert from
     * @param Currency $toCurrency The currency to convert to
     * @return float The exchange rate between the two currencies
     *
     * @throws InvalidArgumentException If the exchange rate is not found
     */
    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float
    {
        $fromCode = $fromCurrency->getCode();
        $toCode = $toCurrency->getCode();

        if (!isset($this->exchangeRates[$fromCode][$toCode])) {
            throw new InvalidArgumentException("Exchange rate not found for {$fromCode} to {$toCode}.");
        }

        return $this->exchangeRates[$fromCode][$toCode];
    }

    /**
     * Sets the exchange rate for a given currency pair.
     *
     * @param string $fromCurrencyCode The currency code to convert from
     * @param string $toCurrencyCode The currency code to convert to
     * @param float|null $rate The exchange rate between the two currencies, or null to remove the rate
     */
    public function setExchangeRate(string $fromCurrencyCode, string $toCurrencyCode, ?float $rate): void
    {
        if ($rate === null) {
            unset($this->exchangeRates[$fromCurrencyCode][$toCurrencyCode]);
        } else {
            $this->exchangeRates[$fromCurrencyCode][$toCurrencyCode] = $rate;
        }
    }
}
