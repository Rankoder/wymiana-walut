<?php
declare(strict_types=1);

namespace src\Application\Exchange;

use src\Domain\{CurrencyCode, Exchange\Service, Money};

/**
 * ExchangeCurrencyHandler is responsible for exchange money from one currency to another.
 */
class ExchangeCurrencyHandler implements ExchangeCurrencyHandlerInterface
{
    private Service $exchangeService;

    public function __construct(Service $exchangeService){
        $this->exchangeService = $exchangeService;
    }

    /**
     * @throws \Exception
     */
    public function handler(ExchangeCurrencyCommand $command): Money
    {
       $fromCurrency = new CurrencyCode($command->getFromCurrency());
       $toCurrency = new CurrencyCode($command->getToCurrency());
       $money = new Money($command->getAmount(), $fromCurrency, $toCurrency);

       return $this->exchangeService->exchange($money, $command->isBuying());
    }
}
