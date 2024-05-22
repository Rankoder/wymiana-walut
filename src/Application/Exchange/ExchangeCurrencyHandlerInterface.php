<?php
declare(strict_types=1);

namespace src\Application\Exchange;

use src\Domain\{Money};

interface ExchangeCurrencyHandlerInterface
{
    public function handler(ExchangeCurrencyCommand $command): Money;
}
