<?php
declare(strict_types=1);

namespace src\Application\Exchange;
interface ExchangeCurrencyCommandInterface
{
    public function getFromCurrency(): string;
    public function getToCurrency(): string;
    public function getAmount(): int;
    public function isBuying(): bool;
}
