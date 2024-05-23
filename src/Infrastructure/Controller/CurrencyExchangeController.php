<?php

namespace src\Infrastructure\Controller;

use src\Application\Service\CurrencyExchangeService;
use src\Domain\Entity\CurrencyExchange;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyExchangeController
 *
 * Controller for handling currency exchange requests.
 */
class CurrencyExchangeController
{
    private CurrencyExchangeService $currencyExchangeService;

    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function convert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer): string
    {
        // Multiply the amount by 100 to convert to the smallest unit (e.g., cents)
        $amountInSmallestUnit = bcmul($amount, '100', 0);
        $amountMoney = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
        $currencyExchange = new CurrencyExchange($amountMoney, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);

        return $this->currencyExchangeService->convert($currencyExchange);
    }
}
