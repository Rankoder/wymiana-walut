<?php

namespace src;

use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use Money\Money;

/**
 * Class Test
 *
 * Provides functionality for currency conversion and fee calculation.
 */
class Test
{
    private string $ratio;
    private string $fromCurrency;
    private string $toCurrency;
    private string $amount;
    private bool $isBuyer;
    private array $exchangeRates;

    /**
     * Test constructor.
     *
     * @param string $fromCurrency The original currency code.
     * @param string $toCurrency The target currency code.
     * @param string $amount The amount to be converted.
     * @param string $ratio The exchange ratio.
     * @param bool $isBuyer It checks whether it is the buyer or the seller and the fee depends on this.
     */
    public function __construct(string $fromCurrency, string $toCurrency, string $amount, string $ratio, bool $isBuyer)
    {
        $this->fromCurrency = $this->sanitizeCurrencyCode($fromCurrency);
        $this->toCurrency = $this->sanitizeCurrencyCode($toCurrency);
        $this->amount = $amount;
        $this->ratio = $ratio;
        $this->buyer = $isBuyer;

        $this->exchangeRates = [
            $this->fromCurrency => [
                $this->toCurrency => $this->ratio
            ]
        ];
    }

    /**
     * Converts the amount from one currency to another and applies a fee if applicable.
     *
     * @return string The converted amount after applying the fee, formatted as a decimal.
     */
    public function convert(): string
    {
        $moneyAmount = $this->parseAmount($this->amount, $this->fromCurrency);
        $newAmountMoneyInNewCurrency = $this->exchangeMoney($moneyAmount, $this->toCurrency);

        list($buyer, $seller) = $this->applyFee($newAmountMoneyInNewCurrency);

        if ($this->buyer) {
            return $this->formatMoney($buyer);
        }

        return $this->formatMoney($newAmountMoneyInNewCurrency->add($seller));
    }

    /**
     * Parses a string amount into a Money object.
     *
     * @param string $amount The amount to parse.
     * @param string $currencyCode The currency code of the amount.
     * @return Money The parsed Money object.
     */
    private function parseAmount(string $amount, string $currencyCode): Money
    {
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        return $moneyParser->parse($amount, new Currency($currencyCode));
    }

    /**
     * Exchanges money from one currency to another.
     *
     * @param Money $money The original Money object.
     * @param string $toCurrency The target currency code.
     * @return Money The converted Money object.
     */
    private function exchangeMoney(Money $money, string $toCurrency): Money
    {
        $currencies = new ISOCurrencies();
        $exchange = new FixedExchange($this->exchangeRates);
        $converter = new Converter($currencies, $exchange);
        return $converter->convert($money, new Currency($toCurrency));
    }

    /**
     * Applies the fee to the converted money.
     *
     * @param Money $money The converted Money object.
     * @return array An array containing the money after fee and the fee itself.
     */
    private function applyFee(Money $money): array
    {
        return $money->allocate([99, 1]);
    }

    /**
     * Formats a Money object into a decimal string.
     *
     * @param Money $money The Money object to format.
     * @return string The formatted amount as a decimal string.
     */
    private function formatMoney(Money $money): string
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        return $moneyFormatter->format($money);
    }

    /**
     * Sanitizes a currency code to prevent code injection.
     *
     * @param string $currencyCode The currency code to sanitize.
     * @return string The sanitized currency code.
     */
    private function sanitizeCurrencyCode(string $currencyCode): string
    {
        return strtoupper(preg_replace('/[^A-Z]/', '', $currencyCode));
    }

    /**
     * Calculates the fee for the exchange.
     *
     * @return Money The fee amount.
     */
    public function calculateFee(): Money
    {
        $moneyAmount = $this->parseAmount($this->amount, $this->fromCurrency);
        $moneyWithoutFee = $this->exchangeMoney($moneyAmount, $this->toCurrency);
        list(, $investorsCut) = $this->applyFee($moneyWithoutFee);

        return $investorsCut;
    }

    /**
     * Converts a Money object to a decimal string.
     *
     * @param Money $money The Money object to convert.
     * @return string The decimal string representation of the money.
     */
    public function convertMoneyToDecimal(Money $money): string
    {
        return $this->formatMoney($money);
    }
}
