<?php

namespace src\Infrastructure\Controller;

use src\Application\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use Money\Currency;
use Money\Money;
use Exception;

/**
 * Class CurrencyExchangeController
 *
 * Controller for handling currency exchange requests.
 */
class CurrencyExchangeController
{
    private CurrencyExchangeService $currencyExchangeService;

    /**
     * CurrencyExchangeController constructor.
     *
     * @param CurrencyExchangeService $currencyExchangeService
     */
    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    /**
     * Converts currency based on given parameters.
     *
     * @param string $fromCurrencyCode
     * @param string $toCurrencyCode
     * @param string $amount
     * @param bool $isBuyer
     * @return string
     */
    public function convert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer): string
    {
        try {
            $amountInSmallestUnit = bcmul($amount, '100', 0);
            $amountMoney = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
            $currencyExchange = new CurrencyExchange($amountMoney, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);

            return $this->currencyExchangeService->convert($currencyExchange);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
