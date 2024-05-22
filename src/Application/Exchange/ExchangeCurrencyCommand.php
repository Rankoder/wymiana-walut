<?php
declare(strict_types=1);

namespace src\Application\Exchange;

/**
 * ExchangeCurrencyCommand is command with set up configuration.
 */
class ExchangeCurrencyCommand implements ExchangeCurrencyCommandInterface
{
    /** @var string  */
    private string $fromCurrency;

    /** @var string  */
    private string $toCurrency;

    /** @var int  */
    private int $amount; //Tutaj będzie "niespodzianka"

    /** @var bool  */
    private bool $isBuying; //Później sprawdź czy to w sumie potrzebne. Teoretycznie tak by rozpoznać kupującego od sprzedającego

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param int $amount
     * @param bool $isBuying
     */
    public function __construct(string $fromCurrency, string $toCurrency, int $amount, bool $isBuying)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->amount = $amount;
        $this->isBuying = $isBuying;
    }

    /**
     * @return string
     */
    public function getFromCurrency(): string
    {
        return $this->fromCurrency;
    }

    /**
     * @return string
     */
    public function getToCurrency(): string
    {
        return $this->toCurrency;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
       return $this->amount;
    }

    /**
     * @return bool
     */
    public function isBuying(): bool
    {
        return $this->isBuying;
    }
}
