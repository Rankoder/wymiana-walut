<?php

namespace src\Application\Controller;

use src\Domain\Service\CurrencyExchangeService;
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

    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function convert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer): string
    {
        try {
            $amountInSmallestUnit = bcmul($amount, '100', 0);
            $amountMoney = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
            $currencyExchange = new CurrencyExchange($amountMoney, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);
            $dto = $this->currencyExchangeService->convert($currencyExchange);

            return $dto->getFormattedAmount();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
