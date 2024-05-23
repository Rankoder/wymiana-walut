<?php
declare(strict_types=1);

namespace src\Application\Controller;

use src\Application\DTO\MoneyDTO;
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
    /** @var CurrencyExchangeService Service responsible for exchange currencies */
    private CurrencyExchangeService $currencyExchangeService;

    /**
     * @param CurrencyExchangeService $currencyExchangeService
     */
    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function convert(string $fromCurrencyCode, string $toCurrencyCode, string $amount, bool $isBuyer): MoneyDTO
    {
        try {
            $amountInSmallestUnit = bcmul($amount, '100', 0);
            $amountMoney = new Money($amountInSmallestUnit, new Currency($fromCurrencyCode));
            $currencyExchange = new CurrencyExchange($amountMoney, new Currency($fromCurrencyCode), new Currency($toCurrencyCode), $isBuyer);
            [$formattedAmount, $currencyCode] = $this->currencyExchangeService->convert($currencyExchange);

            return new MoneyDTO($formattedAmount, $currencyCode);
        } catch (Exception $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }
}
