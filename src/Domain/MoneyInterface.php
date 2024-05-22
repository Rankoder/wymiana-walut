<?php
declare(strict_types=1);

namespace src\Domain;

/**
 * Money Interface
 */
interface MoneyInterface
{
    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @return string
     */
    public function getCurrencyCode(): string;
}
