<?php

namespace src\Domain\Service;

use src\Domain\Entity\CurrencyExchange;
use src\Application\DTO\MoneyDTO;
use src\Domain\Repository\ExchangeRateRepository;
use src\Domain\Repository\FeePercentageRepository;
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
    private FeePercentageRepository $feePercentageRepository;

    public function __construct(ExchangeRateRepository $exchangeRateRepository, FeePercentageRepository $feePercentageRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->feePercentageRepository = $feePercentageRepository;
    }

    public function convert(CurrencyExchange $currencyExchange): MoneyDTO
    {
        $exchangeRate = $this->getExchangeRate($currencyExchange);
        $converter = $this->getConverter($currencyExchange, $exchangeRate);
        $moneyWithoutFee = $this->convertMoney($converter, $currencyExchange);
        $finalAmount = $this->applyFee($moneyWithoutFee, $currencyExchange->isBuyer());
        $formattedAmount = $this->formatMoney($finalAmount);

        return new MoneyDTO($finalAmount, $formattedAmount);
    }

    private function getExchangeRate(CurrencyExchange $currencyExchange): string
    {
        return (string)$this->exchangeRateRepository->getExchangeRate($currencyExchange->getFromCurrency(), $currencyExchange->getToCurrency());
    }

    private function getConverter(CurrencyExchange $currencyExchange, string $exchangeRate): Converter
    {
        $exchange = new FixedExchange([
            $currencyExchange->getFromCurrency()->getCode() => [
                $currencyExchange->getToCurrency()->getCode() => $exchangeRate,
            ],
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
        $buyerFee = (int)$this->feePercentageRepository->getBuyerFeePercentage();
        $sellerFee = (int)$this->feePercentageRepository->getSellerFeePercentage();
        list($buyerAmount, $sellerAmount) = $moneyWithoutFee->allocate([$buyerFee, $sellerFee]);

        return $isBuyer ? $buyerAmount : $moneyWithoutFee->add($sellerAmount);
    }

    private function formatMoney(Money $money): string
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        return $moneyFormatter->format($money);
    }
}
