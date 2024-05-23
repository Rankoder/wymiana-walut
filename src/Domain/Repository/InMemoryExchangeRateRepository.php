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
     * @inheritDoc
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
}
