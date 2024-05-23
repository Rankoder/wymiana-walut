<?php
declare(strict_types=1);

namespace src\Domain\Service;

use src\Domain\Entity\CurrencyExchange;
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
    /**
     * @var ExchangeRateRepository Repository for retrieving exchange rates
     */
    private ExchangeRateRepository $exchangeRateRepository;

    /**
     * @var FeePercentageRepository Repository for retrieving fee percentages
     */
    private FeePercentageRepository $feePercentageRepository;

    /**
     * CurrencyExchangeService constructor.
     *
     * @param ExchangeRateRepository $exchangeRateRepository Repository for retrieving exchange rates
     * @param FeePercentageRepository $feePercentageRepository Repository for retrieving fee percentages
     */
    public function __construct(ExchangeRateRepository $exchangeRateRepository, FeePercentageRepository $feePercentageRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->feePercentageRepository = $feePercentageRepository;
    }

    /**
     * Converts the amount of money from one currency to another.
     *
     * @param CurrencyExchange $currencyExchange The currency exchange transaction
     * @return array An array containing the formatted amount and currency code
     */
    public function convert(CurrencyExchange $currencyExchange): array
    {
        $exchangeRate = $this->getExchangeRate($currencyExchange);
        $converter = $this->getConverter($currencyExchange, $exchangeRate);
        $moneyWithoutFee = $this->convertMoney($converter, $currencyExchange);
        $finalAmount = $this->applyFee($moneyWithoutFee, $currencyExchange->isBuyer());
        $formattedAmount = $this->formatMoney($finalAmount);

        return [$formattedAmount, $finalAmount->getCurrency()->getCode()];
    }

    /**
     * Retrieves the exchange rate for the given currency exchange transaction.
     *
     * @param CurrencyExchange $currencyExchange The currency exchange transaction
     * @return string The exchange rate as a string
     */
    private function getExchangeRate(CurrencyExchange $currencyExchange): string
    {
        return (string)$this->exchangeRateRepository->getExchangeRate($currencyExchange->getFromCurrency(), $currencyExchange->getToCurrency());
    }

    /**
     * Creates a converter for the given currency exchange transaction.
     *
     * @param CurrencyExchange $currencyExchange The currency exchange transaction
     * @param string $exchangeRate The exchange rate for the transaction
     * @return Converter The converter for the transaction
     */
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

    /**
     * Converts the amount of money using the given converter.
     *
     * @param Converter $converter The converter for the transaction
     * @param CurrencyExchange $currencyExchange The currency exchange transaction
     * @return Money The converted amount of money
     */
    private function convertMoney(Converter $converter, CurrencyExchange $currencyExchange): Money
    {
        return $converter->convert($currencyExchange->getAmount(), $currencyExchange->getToCurrency());
    }

    /**
     * Applies the appropriate fee to the converted amount of money.
     *
     * @param Money $moneyWithoutFee The converted amount of money without the fee
     * @param bool $isBuyer Whether the transaction is for a buyer
     * @return Money The final amount of money after applying the fee
     */
    private function applyFee(Money $moneyWithoutFee, bool $isBuyer): Money
    {
        $buyerFee = (int)$this->feePercentageRepository->getBuyerFeePercentage();
        $sellerFee = (int)$this->feePercentageRepository->getSellerFeePercentage();
        list($buyerAmount, $sellerAmount) = $moneyWithoutFee->allocate([$buyerFee, $sellerFee]);

        return $isBuyer ? $buyerAmount : $moneyWithoutFee->add($sellerAmount);
    }

    /**
     * Formats the given amount of money.
     *
     * @param Money $money The amount of money to format
     * @return string The formatted amount of money
     */
    private function formatMoney(Money $money): string
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        return $moneyFormatter->format($money);
    }
}
