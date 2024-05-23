<?php

namespace src\Application\Service;

use src\Domain\Entity\CurrencyExchange;
use src\Domain\Repository\ExchangeRateRepository;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * Class CurrencyExchangeService
 *
 * Service for handling currency exchange operations.
 */
class CurrencyExchangeService
{
    private ExchangeRateRepository $exchangeRateRepository;

    public function __construct(ExchangeRateRepository $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function convert(CurrencyExchange $currencyExchange): string
    {
        $exchangeRate = $this->getExchangeRate($currencyExchange);
        $converter = $this->getConverter($currencyExchange, $exchangeRate);
        $moneyWithoutFee = $this->convertMoney($converter, $currencyExchange);
        $finalAmount = $this->applyFee($moneyWithoutFee, $currencyExchange->isBuyer());

        return $this->formatMoney($finalAmount);
    }

    private function getExchangeRate(CurrencyExchange $currencyExchange): string
    {
        return $this->exchangeRateRepository->getExchangeRate($currencyExchange->getFromCurrency(), $currencyExchange->getToCurrency());
    }

    private function getConverter(CurrencyExchange $currencyExchange, string $exchangeRate): Converter
    {
        $exchange = new FixedExchange([
            $currencyExchange->getFromCurrency()->getCode() => [
                $currencyExchange->getToCurrency()->getCode() => (string)$exchangeRate
            ]
        ]);

        $currencies = new ISOCurrencies();
        return new Converter($currencies, $exchange);
    }

    private function convertMoney(Converter $converter, CurrencyExchange $currencyExchange): Money
    {
        return $converter->convert($currencyExchange->getAmount(), $currencyExchange->getToCurrency());
    }

    private function applyFee(Money $moneyWithoutFee, bool $isBuyer): Money
    {
        list($buyerAmount, $sellerAmount) = $moneyWithoutFee->allocate([99, 1]);
        return $isBuyer ? $buyerAmount : $moneyWithoutFee->add($sellerAmount);
    }

    private function formatMoney(Money $money): string
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        return $moneyFormatter->format($money);
    }
}
